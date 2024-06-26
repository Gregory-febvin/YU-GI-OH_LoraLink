import tkinter as tk
from tkinter import ttk, messagebox
import mysql.connector
from datetime import date
from itertools import combinations
import random

class TournamentManager:
    def __init__(self, host, database, user, password):
        self.conn = mysql.connector.connect(
            host=host,
            database=database,
            user=user,
            password=password
        )
        self.create_tables()
    
    def create_tables(self):
        with self.conn.cursor() as cursor:
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

    def create_tournament(self, name):
        with self.conn.cursor() as cursor:
            cursor.execute("INSERT INTO tournament (name, date, status) VALUES (%s, CURRENT_DATE + INTERVAL 7 DAY, %s)", (name, "ongoing"))
        self.conn.commit()

        # Db du site d'inscription joueur
        second_conn = mysql.connector.connect(
            host='127.0.0.1',
            user='yugioh',
            password='yugioh',
            database='yugioh'
        )

        with second_conn.cursor() as second_cursor:
            second_cursor.execute("INSERT INTO tournoi (type_tournoi, name, nb_player) VALUES (%s, %s, %s)", (1, name, 32))
        second_conn.commit()
        second_conn.close()

    def get_tournaments(self):
        with self.conn.cursor() as cursor:
            cursor.execute("SELECT * FROM tournament")
            tournaments = cursor.fetchall()
        return tournaments

    def get_rounds(self, tournament_id):
        with self.conn.cursor() as cursor:
            cursor.execute("SELECT round_number, round_date FROM round WHERE tournament_id = %s", (tournament_id,))
            rounds = cursor.fetchall()
        return rounds

    def get_matches(self, round_id):
        with self.conn.cursor() as cursor:
            cursor.execute("SELECT * FROM matche WHERE round_id = %s", (round_id,))
            matches = cursor.fetchall()
        return matches

    def create_match(self, tournament_id, round_number, player1_id, player2_id, table_number):
        with self.conn.cursor() as cursor:
            cursor.execute("""
                INSERT INTO matche (round_id, player1_id, player2_id, num_table) 
                VALUES (
                    (SELECT round_id FROM round WHERE tournament_id = %s AND round_number = %s), 
                    %s, %s, %s
                )""", (tournament_id, round_number, player1_id, player2_id, table_number))
        self.conn.commit()

    def create_round(self, tournament_id):
        with self.conn.cursor() as cursor:
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
                    messagebox.showerror("Error", "Cannot create new round until all matches of the previous round are finished.")
                    return

            cursor.execute("INSERT INTO round (tournament_id, round_number, round_date) VALUES (%s, %s, %s)", (tournament_id, round_number, date.today()))
        self.conn.commit()

        # Récupérer tous les joueurs et leurs points pour le tournoi donné
        with self.conn.cursor() as cursor:
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

        # Générer la liste des numéros de table disponibles
        table_numbers = list(range(1, max_matches + 1))

        # Créer des matches en associant les joueurs les uns aux autres en fonction du nombre maximal de matchs
        matches = []
        while players and table_numbers:
            player1_id, player1_score = players.pop(0)
            player2_id, player2_score = players.pop(0)
            table_number = table_numbers.pop(0)
            matches.append((player1_id, player2_id, table_number))

        # Insérer les matches dans la base de données
        for match in matches:
            self.create_match(tournament_id, round_number, match[0], match[1], match[2])

    def record_match_result(self, match_id, winner_id):
        with self.conn.cursor() as cursor:
            cursor.execute("UPDATE matche SET winner_id = %s, isFinish = 1 WHERE match_id = %s", (winner_id, match_id))
            cursor.execute("UPDATE userscore SET score = score + 3 WHERE user_id = (SELECT player1_id FROM matche WHERE match_id = %s) AND user_id = %s", (match_id, winner_id))
            cursor.execute("UPDATE userscore SET score = score + 2 WHERE user_id = (SELECT player2_id FROM matche WHERE match_id = %s) AND user_id = %s", (match_id, winner_id))
        self.conn.commit()

    def all_matches_finished(self, round_id):
        with self.conn.cursor() as cursor:
            cursor.execute("SELECT COUNT(*) FROM matche WHERE round_id = %s AND isFinish = 0", (round_id,))
            matches = cursor.fetchone()[0]
        return matches == 0

    def get_player_scores(self, tournament_id):
        with self.conn.cursor() as cursor:
            cursor.execute("SELECT user.username, userscore.score FROM userscore JOIN user ON userscore.user_id = user.user_id WHERE userscore.tournament_id = %s ORDER BY userscore.score DESC", (tournament_id,))
            scores = cursor.fetchall()
        return scores

    def get_username(self, user_id):
        with self.conn.cursor() as cursor:
            cursor.execute("SELECT username FROM user WHERE user_id = %s", (user_id,))
            username = cursor.fetchone()[0]
        return username

    def get_players_for_tournament(self, tournament_id):
        with self.conn.cursor() as cursor:
            cursor.execute("SELECT user_id FROM usertournament WHERE tournament_id = %s", (tournament_id,))
            players = cursor.fetchall()
        return [player[0] for player in players]


class TournamentApp:
    def __init__(self, root):
        self.manager = TournamentManager("127.0.0.1", "test_yu", "yugioh", "yugioh")
        self.root = root
        self.root.title("Tournament Manager")

        self.main_frame = ttk.Frame(root, padding="10")
        self.main_frame.grid(row=0, column=0, sticky=(tk.W, tk.E, tk.N, tk.S))

        self.tournament_name_label = ttk.Label(self.main_frame, text="Tournament Name:")
        self.tournament_name_label.grid(row=0, column=0, sticky=tk.W)
        self.tournament_name_label.grid_configure(ipadx=5, ipady=5)

        self.tournament_name_entry = ttk.Entry(self.main_frame)
        self.tournament_name_entry.grid(row=0, column=1, sticky=(tk.W, tk.E))   

        self.create_tournament_button = ttk.Button(self.main_frame, text="Create Tournament", command=self.create_tournament)
        self.create_tournament_button.grid(row=0, column=2, sticky=tk.W)

        self.refresh_button = ttk.Button(self.main_frame, text="Refresh", command=self.update_tournament_list)
        self.refresh_button.grid(row=0, column=3, sticky=(tk.W, tk.E))

        self.tournament_list = ttk.Treeview(self.main_frame, columns=("ID", "Name", "Date", "Status"), show="headings")
        self.tournament_list.heading("ID", text="ID")
        self.tournament_list.heading("Name", text="Name")
        self.tournament_list.heading("Date", text="Date")
        self.tournament_list.heading("Status", text="Status")
        self.tournament_list.grid(row=1, column=0, columnspan=4, sticky=(tk.W, tk.E, tk.N, tk.S))
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
        self.top.title(f"List Round")

        self.round_frame = ttk.Frame(self.top, padding="10")
        self.round_frame.grid(row=0, column=0, sticky=(tk.W, tk.E, tk.N, tk.S))

        self.round_list = ttk.Treeview(self.round_frame, columns=("Number", "Date"), show="headings")
        self.round_list.heading("Number", text="Round Number")
        self.round_list.heading("Date", text="Date")
        self.round_list.grid(row=0, column=0, columnspan=3, sticky=(tk.W, tk.E, tk.N, tk.S))
        self.round_list.bind("<Double-1>", self.select_round)

        self.create_round_button = ttk.Button(self.round_frame, text="Create Round", command=self.create_round)
        self.create_round_button.grid(row=1, column=0, columnspan=3, sticky=tk.W)

        self.refresh_button = ttk.Button(self.round_frame, text="Refresh", command=self.update_round_list)
        self.refresh_button.grid(row=2, column=0, columnspan=3, sticky=tk.W)

        # Appel à la méthode d'actualisation lors de l'initialisation
        self.update_round_list()

    def create_round(self):
        self.manager.create_round(self.tournament_id)
        self.update_round_list()

    def update_round_list(self):
        for item in self.round_list.get_children():
            self.round_list.delete(item)

        # Mettre à jour la liste des rounds
        rounds = self.manager.get_rounds(self.tournament_id)
        for round in rounds:
            self.round_list.insert("", "end", values=(round[0], round[1]))

    def select_round(self, event):
        selected_item = self.round_list.selection()[0]
        round_number = self.round_list.item(selected_item)["values"][0]
        round_id = self.get_round_id(round_number)
        RoundDetailWindow(self.top, self.manager, round_id, self.tournament_id)

    def get_round_id(self, round_number):
        cursor = self.manager.conn.cursor()
        cursor.execute("SELECT round_id FROM round WHERE round_number = %s AND tournament_id = %s", (round_number, self.tournament_id))
        round_id = cursor.fetchone()[0]
        
        cursor.close()
        return round_id

class RoundDetailWindow:
    def __init__(self, parent, manager, round_id, tournament_id):
        self.manager = manager
        self.round_id = round_id
        self.tournament_id = tournament_id

        self.top = tk.Toplevel(parent)
        self.top.title(f"List match")

        self.match_frame = ttk.Frame(self.top, padding="10")
        self.match_frame.grid(row=0, column=0, sticky=(tk.W, tk.E, tk.N, tk.S))

        self.match_list = ttk.Treeview(self.match_frame, columns=("ID", "Player 1", "Player 2", "Winner", "Finished", "Judge Call", "Table"), show="headings")
        self.match_list.heading("ID", text="ID")
        self.match_list.heading("Player 1", text="Player 1")
        self.match_list.heading("Player 2", text="Player 2")
        self.match_list.heading("Winner", text="Winner")
        self.match_list.heading("Finished", text="Finished")
        self.match_list.heading("Judge Call", text="Judge Call")
        self.match_list.heading("Table", text="Table")
        self.match_list.grid(row=0, column=0, columnspan=4, sticky=(tk.W, tk.E, tk.N, tk.S))
        self.match_list.bind("<Double-1>", self.select_match)

        self.create_matches_button = ttk.Button(self.match_frame, text="Create Matches", command=self.create_matches)
        self.create_matches_button.grid(row=1, column=0, columnspan=4, sticky=tk.W)

        self.refresh_button = ttk.Button(self.match_frame, text="Refresh", command=self.update_match_list)
        self.refresh_button.grid(row=1, column=1, sticky=tk.W)

        self.update_match_list()

    def update_match_list(self):
        for item in self.match_list.get_children():
            self.match_list.delete(item)

        matches = self.manager.get_matches(self.round_id)
        for match in matches:
            player1_name = self.manager.get_username(match[2])
            player2_name = self.manager.get_username(match[3])
            winner_name = self.manager.get_username(match[4]) if match[4] else ""
            finished = "Oui" if match[5] else "Non"
            judge_call = "Oui" if match[6] else "Non"
            self.match_list.insert("", "end", values=(match[0], f"{player1_name} ({match[2]})", f"{player2_name} ({match[3]})", f"{winner_name} ({match[4]})", finished, judge_call, match[7]))

    def create_matches(self):
        players = self.manager.get_players_for_tournament(self.tournament_id)
        if not players:
            messagebox.showwarning("No Players", "There are no players registered for this tournament.")
            return

        players = random.sample(players, len(players))  # Shuffle the list
        num_tables = min(len(players) // 2, 10)  # Assume maximum 10 tables
        matches = []

        for i in range(num_tables):
            player1 = players.pop()
            player2 = players.pop()
            matches.append((player1, player2, i + 1))

        for player1, player2, table_number in matches:
            self.manager.create_match(self.tournament_id, self.round_number, player1, player2, table_number)

        self.update_match_list()
        self.check_all_matches_finished()

    def select_match(self, event):
        selected_item = self.match_list.selection()[0]
        match_id = self.match_list.item(selected_item)["values"][0]
        MatchDetailWindow(self.top, self.manager, match_id)


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