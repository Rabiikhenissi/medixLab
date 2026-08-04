# MedixLab — Connecter un analyseur (machine de laboratoire)

L'appli peut dialoguer avec un analyseur de 3 façons. Le **protocole** est choisi
par machine dans `Centre → Configuration des machines`.

| Protocole | Type de liaison | Usage type |
|---|---|---|
| **HL7 / MLLP (TCP)** | Réseau Ethernet | Analyseur sur le réseau avec un port d'écoute MLLP (souvent 5001) |
| **HL7 / Série (RS-232 / USB)** | Câble direct PC ↔ analyseur | Vieil analyseur à port série (DB9 / DB25) via adaptateur USB-série |
| **HTTP / JSON** | Réseau HTTP | Simulateur / API maison (endpoints `/api/order`, `/api/status`) |

Quel que soit le protocole, la commande envoyée est un message HL7 `ORM^O01`
(ordre d'examen) et la réponse attendue est un `ORU^R01` (résultat). En TCP et
en Série, le message est encapsulé en **MLLP** : `\x0b` … message … `\x1c\x0d`.

---

## 1. Branchement physique (mode Série uniquement)

1. **Câble** : relier l'analyseur au PC avec un câble série. Un câble **null-modem**
   (croisé) est souvent requis entre deux équipements DTE (analyseur ↔ PC).
   Si l'analyseur n'a pas de port série, il faut un **adaptateur USB ↔ RS-232**
   (la plupart des analyseurs modernes sortent un port virtuel COM par USB).
2. **Pilotes** : installer le driver de l'adaptateur USB-série (ex. CH340,
   FTDI, CP210x). Sur Windows, le port apparaît dans le Gestionnaire de
   périphériques sous **Ports (COM et LPT)** ; sur Linux, sous
   `/dev/ttyUSB0` ou `/dev/ttyACM0`.

> Les analyseurs série n'ont **pas d'adresse IP** : le mode Série ignore les
> champs « Hôte / Port ». Le service teste simplement que le port série
> s'ouvre avant d'envoyer.

---

## 2. Identifier le port série

**Windows (PowerShell)**
```powershell
Get-WmiObject Win32_SerialPort | Select Name, DeviceID
# OU
mode            # liste tous les ports COM disponibles
mode COM5       # montre la config actuelle d'un port
```

**Linux**
```bash
ls -l /dev/ttyUSB* /dev/ttyACM* /dev/ttyS*
stty -F /dev/ttyUSB0 9600 cs8 -cstopb -parenb
stty -F /dev/ttyUSB0 -a   # affiche la config actuelle
```

Nom de port typique : `COM3` (Windows, COM1–COM9) ou `\\.\COM10` pour COM ≥ 10 ;
`/dev/ttyUSB0`, `/dev/ttyACM0`, `/dev/ttyS0` (Linux).

---

## 3. Configuration dans l'application

1. Aller dans **Centre → Configuration des machines → Nouvelle machine**.
2. Renseigner un **nom** (ex. « Hématologie Coulter »).
3. Choisir le **protocole** :
   - **HL7 / Série (RS-232 / USB)** pour un câble direct.
   - **HL7 / MLLP (TCP)** pour un analyseur réseau.
   - **HTTP / JSON** pour le simulateur/API.
4. Selon le protocole, remplir :
   - **Série** : port série (`COM5`), débit (**9600** baud), 8 bits de données,
     1 bit d'arrêt, parité **Aucune (N)** — la config standard des analyseurs.
     Ajustez avec le manuel de l'appareil si besoin.
   - **TCP** : adresse IP (ex. `192.168.1.100`), **Port MLLP** (ex. `5001`).
   - **HTTP** : adresse IP + **Port HTTP** (ex. `5000`), éventuellement API key.
5. **Timeout** : 15 s par défaut.
6. Activer la machine et enregistrer.
7. Cliquer **« Tester la connexion »** sur la carte de la machine : il ouvre le
   socket TCP ou le port série selon le protocole et affiche en ligne/hors ligne.

---

## 4. Parcours d'une commande

`MachineService->sendOrder()` route selon le protocole :

| Protocole | Méthode interne |
|---|---|
| `serial_hl7` | `sendViaSerialHl7()` — `SerialClient` (MLLP sur port série) |
| `hl7_mllp` | `sendViaHl7()` — `MllpClient` (MLLP sur TCP) |
| `http_json` | `sendViaHttp()` — HTTP/JSON |

Si le transport choisi est **indisponible** (port série déjà utilisé, machine
hors ligne…), le service retombe automatiquement sur le simulateur HTTP
(ou le générateur interne) et enregistre le résultat quand même — la valeur
`source` dans la réponse indique `hl7_serial`, `hl7_mllp`, `http_json` ou
`internal`.

---

## 5. Résolution de problèmes

- **« Port série occupé » / ne s'ouvre pas** : un autre logiciel (terminal,
  driver propriétaire de l'analyseur) utilise le même COM. Fermez-le.
- **Windows** : vérifiez `mode COM5` ; si le port n'apparaît pas, réinstallez
  le driver USB-série.
- **Linux** : ajoutez l'utilisateur au groupe `dialout` et redémarrez la
  session, sinon le port est refusé (`Permission denied`).
- **Parité / débit faux** : l'analyseur renvoie du bruit ou ne répond pas.
  Respectez la config du manuel (par défaut 9600 8 N 1).
- **Aucune réponse** : la machine n'utilise peut-être pas le MLLP ; certains
  analyseurs série attendent un simple message HL7 terminé par CR/LF sans
  `\x0b`/`\x1c\x0d`.
- **Test série hors ligne sur le serveur** : l'app doit tourner **sur le PC
  physiquement relié** à l'analyseur (port série local). Un serveur distant
  ne peut pas ouvrir le COM d'un autre poste.

## 6. Variables d'environnement (optionnel)

Le `config/machine.php` propose des valeurs par défaut utilisables sans passer
par l'UI :

```
MACHINE_PROTOCOL=serial_hl7
MACHINE_SERIAL_PORT=COM5
MACHINE_BAUD_RATE=9600
MACHINE_DATA_BITS=8
MACHINE_STOP_BITS=1
MACHINE_PARITY=N
```
