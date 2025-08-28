import time
import random
import logging

# Configuration du logger
logging.basicConfig(
    filename="app.log",  # fichier de sortie
    level=logging.INFO,
    format="%(asctime)s - %(levelname)s - %(message)s"
)

# Quelques logs types
logs_possibles = [
    ("INFO", "Nouvelle connexion utilisateur"),
    ("INFO", "Requête GET /api/v1/resource traitée"),
    ("WARNING", "Temps de réponse lent détecté"),
    ("ERROR", "Erreur 404: Ressource introuvable"),
    ("INFO", "Traitement terminé avec succès"),
    ("ERROR", "Échec d'authentification utilisateur"),
]

def envoyer_logs():
    niveau, message = random.choice(logs_possibles)
    if niveau == "INFO":
        logging.info(message)
    elif niveau == "WARNING":
        logging.warning(message)
    elif niveau == "ERROR":
        logging.error(message)

if __name__ == "__main__":
    while True:
        envoyer_logs()
        time.sleep(180)  # attend 3 minutes
