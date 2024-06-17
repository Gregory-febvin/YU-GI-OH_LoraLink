import paho.mqtt.client as mqtt
import json
import mysql.connector

# Configuration TTN
ttn_app_id = "projet-yu-gi-oh"
ttn_access_key = "NNSXS.FLU33GCAZNK6NJ7EYHF7TYML7JE2PWSOLPI2YWQ.E2J6JUNNZIV7B647YKTHBY5SZEOHBSVCHPQBNEWDV3HED7U54RTQ"

# Configuration base de données
db_config = {
    'user': 'yugioh',
    'password': 'yugioh',
    'host': '127.0.0.1',
    'database': 'test_yu'
}

def update_database(num_table):
    try:
        # Connexion à la base de données
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor()

        # Requête de mise à jour
        query = """
        UPDATE `matche` SET `callJudge`= 1 
        WHERE `num_table`= %s 
        AND `round_id` = (SELECT MAX(`round_id`) FROM `matche`)
        """
        
        # Exécution de la requête
        cursor.execute(query, (num_table,))
        conn.commit()

        print(f"Table {num_table} updated successfully.")

    except mysql.connector.Error as err:
        print(f"Error: {err}")
    finally:
        cursor.close()
        conn.close()

def on_connect(client, userdata, flags, rc):
    print("Connecté au broker MQTT avec le code de résultat " + str(rc))
    client.subscribe(f"v3/{ttn_app_id}@ttn/devices/+/up")

def on_message(client, userdata, msg):
    print(f"Message reçu sur le topic {msg.topic}")
    payload = json.loads(msg.payload.decode())
    #print("Payload: ", payload)
    
    try:
        # Récupérer le decoded_payload
        num_table = payload['uplink_message']['decoded_payload']['bytes'][0]
        
        print(f"Decoded payload: {num_table}")
        
        # Mettre à jour la base de données
        update_database(num_table)
        
    except KeyError as err:
        print(f"Erreur: la clé {err} est manquante dans le payload.")
    except mysql.connector.Error as err:
        print(f"Erreur de base de données: {err}")

client = mqtt.Client()
client.username_pw_set(ttn_app_id, ttn_access_key)
client.on_connect = on_connect
client.on_message = on_message

client.connect("eu1.cloud.thethings.network", 1883, 60)

client.loop_forever()