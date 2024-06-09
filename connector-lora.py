import paho.mqtt.client as mqtt
import json
import base64
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
        # Récupérer le frm_payload
        frm_payload_base64 = payload['uplink_message']['frm_payload']
        
        # Décoder le frm_payload de base64
        frm_payload_bytes = base64.b64decode(frm_payload_base64)
        
        # Convertir les bytes en chaîne ASCII
        frm_payload_ascii = frm_payload_bytes.decode('ascii')
        
        # Essayer de parser la chaîne ASCII en JSON
        try:
            frm_payload_json = json.loads(frm_payload_ascii)
            print("frm_payload JSON: ", frm_payload_json)
            
            # Récupérer le numéro de table et mettre à jour la base de données
            num_table = frm_payload_json['Table']
            update_database(num_table)
            
        except json.JSONDecodeError:
            print("frm_payload ASCII (non-JSON): ", frm_payload_ascii)
    except KeyError:
        print("Le payload ne contient pas 'frm_payload'.")

client = mqtt.Client()
client.username_pw_set(ttn_app_id, ttn_access_key)
client.on_connect = on_connect
client.on_message = on_message

client.connect("eu1.cloud.thethings.network", 1883, 60)

client.loop_forever()
