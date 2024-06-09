import tkinter as tk
from tkinter import ttk, messagebox
import mysql.connector
from datetime import date
from itertools import combinations

class TournamentManager:
    def __init__(self, host, database, user, password):
        self.conn = mysql.connector.connect(
            host="127.0.0.1",
            database="test_yu",
            user="root",
            password=""
        )
        self.create_tables()
    
    def get_username(self, user_id):
        cursor = self.conn.cursor()
        cursor.execute("SELECT username FROM user WHERE user_id = %s", (user_id,))
        username = cursor.fetchone()[0]
        cursor.close()
        return username

    def create_tables(self):
        cursor = self.conn.cursor()
        cursor.execute("""
            CREATE TABLE IF NOT EXISTS user (
                user_id INT PRIMARY KEY AUTO_INCREMENT,
                username VARCHAR(50) NOT NULL,
                email VARCHAR(100) NOT NULL,
                password VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        """)
        cursor.execute("""
            CREATE TABLE IF NOT EXISTS tournament (
                tournament_id INT PRIMARY KEY AUTO_INCREMENT,
                name VARCHAR(100) NOT NULL,
                date DATE NOT NULL,
                status VARCHAR(20) NOT NULL
            );
        """)
        cursor.execute("""
            CREATE TABLE IF NOT EXISTS usertournament (
                user_tournament_id INT PRIMARY KEY AUTO_INCREMENT,
                user_id INT NOT NULL,
                tournament_id INT NOT NULL,
                FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE,
                FOREIGN KEY (tournament_id) REFERENCES tournament(tournament_id) ON DELETE CASCADE
            );
        """)
        cursor.execute("""
            CREATE TABLE IF NOT EXISTS userscore (
                user_score_id INT PRIMARY KEY AUTO_INCREMENT,
                user_id INT NOT NULL,
                tournament_id INT NOT NULL,
                score INT NOT NULL DEFAULT 0,
                FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE,
                FOREIGN KEY (tournament_id) REFERENCES tournament(tournament_id) ON DELETE CASCADE
            );
        """)
        cursor.execute("""
            CREATE TABLE IF NOT EXISTS round (
                round_id INT PRIMARY KEY AUTO_INCREMENT,
                tournament_id INT NOT NULL,
                round_number INT NOT NULL,
                round_date DATE NOT NULL,
                FOREIGN KEY (tournament_id) REFERENCES tournament(tournament_id) ON DELETE CASCADE
            );
        """)
        cursor.execute("""
            CREATE TABLE IF NOT EXISTS matche (
                match_id INT PRIMARY KEY AUTO_INCREMENT,
                round_id INT NOT NULL,
                player1_id INT NOT NULL,
                player2_id INT NOT NULL,
                winner_id INT,
                isFinish TINYINT NOT NULL DEFAULT 0,
                callJudge TINYINT NOT NULL DEFAULT 0,
                FOREIGN KEY (round_id) REFERENCES round(round_id) ON DELETE CASCADE,
                FOREIGN KEY (player1_id) REFERENCES user(user_id),
                FOREIGN KEY (player2_id) REFERENCES user(user_id),
                FOREIGN KEY (winner_id) REFERENCES user(user_id)
            );
        """)
        self.conn.commit()
        cursor.close()

    def create_tournament(self, name):
        cursor = self.conn.cursor()
        cursor.execute("INSERT INTO tournament (name, date, status) VALUES (%s, %s, %s)", (name, date.today(), "ongoing"))
        self.conn.commit()
        cursor.close()

    def get_tournaments(self):
        cursor = self.conn.cursor()
        cursor.execute("SELECT * FROM tournament")
        tournaments = cursor.fetchall()
        cursor.close()
        return tournaments

    def get_rounds(self, tournament_id):
        cursor = self.conn.cursor()
        cursor.execute("SELECT round_id, round_number, round_date FROM round WHERE tournament_id = %s", (tournament_id,))
        rounds = cursor.fetchall()
        cursor.close()
        return rounds

    def get_matches(self, round_id):
        cursor = self.conn.cursor()
        cursor.execute("SELECT * FROM matche WHERE round_id = %s", (round_id,))
        matches = cursor.fetchall()
        cursor.close()
        return matches

    def create_round(self, tournament_id):
        cursor = self.conn.cursor()
        cursor.execute("SELECT COUNT(*) FROM round WHERE tournament_id = %s", (tournament_id,))
        rounds = cursor.fetchone()[0]
        round_number = rounds + 1

        # Vérifier si tous les matchs du round précédent sont terminés
        if rounds > 0:
            cursor.execute("""
                SELECT COUNT(*) 
                FROM matche 
                INNER JOIN round ON matche.round_id = round.round_id 
                WHERE round.tournament_id = %s 
                AND round.round_number = %s 
                AND matche.isFinish = 0
            """, (tournament_id, rounds))
            unfinished_matches = cursor.fetchone()[0]
            if unfinished_matches > 0:
                # Afficher un message d'erreur en mode UI
                messagebox.showerror("Error", "Cannot create new round until all matches of the previous round are finished.")
                return

        cursor.execute("INSERT INTO round (tournament_id, round_number, round_date) VALUES (%s, %s, %s)", (tournament_id, round_number, date.today()))
        self.conn.commit()

        # Récupérer tous les joueurs et leurs points pour le tournoi donné
        cursor.execute("""
            SELECT u.user_id, us.score 
            FROM userscore us
            INNER JOIN user u ON us.user_id = u.user_id
            WHERE us.tournament_id = %s 
            ORDER BY us.score DESC
        """, (tournament_id,))
        players = cursor.fetchall()

        # Calculer le nombre maximal de matchs possible en fonction du nombre de joueurs
        num_players = len(players)
        max_matches = num_players // 2

        # Créer des matches en associant les joueurs les uns aux autres en fonction du nombre maximal de matchs
        for _ in range(max_matches):
            # Sélectionner le joueur 1 pour le match
            player1_id, player1_score = players.pop(0)
            # Sélectionner le joueur 2 pour le match
            player2_id, player2_score = None, None
            for player in players:
                player_id, _ = player
                if player_id != player1_id:
                    player2_id, player2_score = player
                    players.remove(player)
                    break
            # Créer le match
            self.create_match(tournament_id, round_number, player1_id, player2_id)

        cursor.close()

    def create_match(self, tournament_id, round_number, player1_id, player2_id):
        cursor = self.conn.cursor()
        cursor.execute("INSERT INTO matche (round_id, player1_id, player2_id) VALUES ((SELECT round_id FROM round WHERE tournament_id = %s AND round_number = %s), %s, %s)", (tournament_id, round_number, player1_id, player2_id))
        self.conn.commit()
        cursor.close()


    def record_match_result(self, match_id, winner_id):
        cursor = self.conn.cursor()
        cursor.execute("UPDATE matche SET winner_id = %s, isFinish = 1 WHERE match_id = %s", (winner_id, match_id))
        cursor.execute("UPDATE userscore SET score = score + 3 WHERE user_id = (SELECT player1_id FROM matche WHERE match_id = %s) AND user_id = %s", (match_id, winner_id))
        cursor.execute("UPDATE userscore SET score = score + 2 WHERE user_id = (SELECT player2_id FROM matche WHERE match_id = %s) AND user_id = %s", (match_id, winner_id))
        self.conn.commit()
        cursor.close()

    def all_matches_finished(self, round_id):
        cursor = self.conn.cursor()
        cursor.execute("SELECT COUNT(*) FROM matche WHERE round_id = %s AND isFinish = 0", (round_id,))
        matches = cursor.fetchone()[0]
        cursor.close()
        return matches == 0

    def get_player_scores(self, tournament_id):
        cursor = self.conn.cursor()
        cursor.execute("SELECT user.username, userscore.score FROM userscore JOIN user ON userscore.user_id = user.user_id WHERE userscore.tournament_id = %s ORDER BY userscore.score DESC", (tournament_id,))
        scores = cursor.fetchall()
        cursor.close()
        return scores

class TournamentApp:
    def __init__(self, root):
        self.manager = TournamentManager("127.0.0.1", "test_yu", "root", "root")
        self.root = root
        self.root.title("Tournament Manager")

        self.main_frame = ttk.Frame(root, padding="10")
        self.main_frame.grid(row=0, column=0, sticky=(tk.W, tk.E, tk.N, tk.S))

        self.tournament_name_label = ttk.Label(self.main_frame, text="Tournament Name:")
        self.tournament_name_label.grid(row=0, column=0, sticky=tk.W)

        self.tournament_name_entry = ttk.Entry(self.main_frame)
        self.tournament_name_entry.grid(row=0, column=1, sticky=(tk.W, tk.E))

        self.create_tournament_button = ttk.Button(self.main_frame, text="Create Tournament", command=self.create_tournament)
        self.create_tournament_button.grid(row=0, column=2, sticky=tk.W)

        self.tournament_list = ttk.Treeview(self.main_frame, columns=("ID", "Name", "Date", "Status"), show="headings")
        self.tournament_list.heading("ID", text="ID")
        self.tournament_list
        self.tournament_list.heading("Name", text="Name")
        self.tournament_list.heading("Date", text="Date")
        self.tournament_list.heading("Status", text="Status")
        self.tournament_list.grid(row=1, column=0, columnspan=3, sticky=(tk.W, tk.E, tk.N, tk.S))
        self.tournament_list.bind("<Double-1>", self.select_tournament)

        self.update_tournament_list()

    def create_tournament(self):
        name = self.tournament_name_entry.get()
        if name:
            self.manager.create_tournament(name)
            self.update_tournament_list()
            self.tournament_name_entry.delete(0, tk.END)
        else:
            messagebox.showwarning("Input Error", "Please enter a tournament name.")

    def update_tournament_list(self):
        for item in self.tournament_list.get_children():
            self.tournament_list.delete(item)

        tournaments = self.manager.get_tournaments()
        for tournament in tournaments:
            self.tournament_list.insert("", "end", values=tournament)

    def select_tournament(self, event):
        selected_item = self.tournament_list.selection()[0]
        tournament_id = self.tournament_list.item(selected_item)["values"][0]
        TournamentDetailWindow(self.root, self.manager, tournament_id)

class TournamentDetailWindow:
    def __init__(self, parent, manager, tournament_id):
        self.manager = manager
        self.tournament_id = tournament_id

        self.top = tk.Toplevel(parent)
        self.top.title(f"Tournament {tournament_id} Details")

        self.round_frame = ttk.Frame(self.top, padding="10")
        self.round_frame.grid(row=0, column=0, sticky=(tk.W, tk.E, tk.N, tk.S))

        self.round_list = ttk.Treeview(self.round_frame, columns=("Number", "Date"), show="headings")
        self.round_list.heading("Number", text="Round Number")
        self.round_list.heading("Date", text="Date")
        self.round_list.grid(row=0, column=0, columnspan=3, sticky=(tk.W, tk.E, tk.N, tk.S))
        self.round_list.bind("<Double-1>", self.select_round)

        self.create_round_button = ttk.Button(self.round_frame, text="Create Round", command=self.create_round)
        self.create_round_button.grid(row=1, column=0, columnspan=3, sticky=tk.W)

        self.update_round_list()

    def create_round(self):
        self.manager.create_round(self.tournament_id)
        self.update_round_list()

    def update_round_list(self):
        for item in self.round_list.get_children():
            self.round_list.delete(item)

        rounds = self.manager.get_rounds(self.tournament_id)
        for round in rounds:
            self.round_list.insert("", "end", values=(round[1], round[2]))

    def select_round(self, event):
        selected_item = self.round_list.selection()[0]
        round_number = self.round_list.item(selected_item)["values"][0]
        round_id = self.get_round_id(round_number)
        RoundDetailWindow(self.top, self.manager, round_id)

    def get_round_id(self, round_number):
        cursor = self.manager.conn.cursor()
        cursor.execute("SELECT round_id FROM round WHERE round_number = %s AND tournament_id = %s", (round_number, self.tournament_id))
        round_id = cursor.fetchone()[0]
        cursor.close()
        return round_id

class RoundDetailWindow:
    def __init__(self, parent, manager, round_id):
        self.manager = manager
        self.round_id = round_id

        self.top = tk.Toplevel(parent)
        self.top.title(f"Round {round_id} Details")

        self.match_frame = ttk.Frame(self.top, padding="10")
        self.match_frame.grid(row=0, column=0, sticky=(tk.W, tk.E, tk.N, tk.S))

        self.match_list = ttk.Treeview(self.match_frame, columns=("ID", "Player 1", "Player 2", "Winner", "Finished"), show="headings")
        self.match_list.heading("ID", text="ID")
        self.match_list.heading("Player 1", text="Player 1")
        self.match_list.heading("Player 2", text="Player 2")
        self.match_list.heading("Winner", text="Winner")
        self.match_list.heading("Finished", text="Finished")
        self.match_list.grid(row=0, column=0, columnspan=4, sticky=(tk.W, tk.E, tk.N, tk.S))
        self.match_list.bind("<Double-1>", self.select_match)

        self.create_matches_button = ttk.Button(self.match_frame, text="Create Matches", command=self.create_matches)
        self.create_matches_button.grid(row=1, column=0, columnspan=4, sticky=tk.W)

        self.update_match_list()

    def update_match_list(self):
        for item in self.match_list.get_children():
            self.match_list.delete(item)

        matches = self.manager.get_matches(self.round_id)
        for match in matches:
            player1_name = self.manager.get_username(match[2])  # Récupération du nom de l'utilisateur du joueur 1
            player2_name = self.manager.get_username(match[3])  # Récupération du nom de l'utilisateur du joueur 2
            winner_name = self.manager.get_username(match[4]) if match[4] else ""  # Récupération du nom de l'utilisateur du vainqueur
            self.match_list.insert("", "end", values=(match[0], f"{player1_name} ({match[2]})", f"{player2_name} ({match[3]})", f"{winner_name} ({match[4]})", match[5]))

    def create_matches(self):
        # This is a placeholder. Replace with logic to get players
        players = [1, 2, 3, 4]  # Example player IDs
        self.manager.create_matches(self.round_id, players)
        self.update_match_list()

    def select_match(self, event):
        selected_item = self.match_list.selection()[0]
        match_id = self.match_list.item(selected_item)["values"][0]
        MatchDetailWindow(self.top, self.manager, match_id)

class MatchDetailWindow:
    def __init__(self, parent, manager, match_id):
        self.manager = manager
        self.match_id = match_id

        self.top = tk.Toplevel(parent)
        self.top.title(f"Match {match_id} Details")
        self.top.geometry("320x140")  # Ajustement de la taille de la fenêtre

        self.winner_label = ttk.Label(self.top, text="Winner ID:")
        self.winner_label.grid(row=0, column=0, sticky=tk.W)

        self.winner_entry = ttk.Entry(self.top)
        self.winner_entry.grid(row=0, column=1, sticky=(tk.W, tk.E))

        self.finish_match_button = ttk.Button(self.top, text="Finish Match", command=self.finish_match)
        self.finish_match_button.grid(row=1, column=0, columnspan=2, sticky=tk.W)

    def finish_match(self):
        winner_id = self.winner_entry.get()
        if winner_id:
            self.manager.record_match_result(self.match_id, int(winner_id))
            self.top.destroy()
        else:
            messagebox.showwarning("Input Error", "Please enter a winner ID.")

if __name__ == "__main__":
    root = tk.Tk()
    app = TournamentApp(root)
    root.mainloop()