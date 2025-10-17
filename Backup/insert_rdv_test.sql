-- Script pour créer 2 rendez-vous aléatoires par patient
-- Entre le 18/10/2025 et le 30/10/2025

-- Liste des patients (ID 5-16)
-- Liste des services : 1=Consultation(30min), 2=Détartrage(30min), 3=Implantologie(90min), 
--                      4=Orthodontie(30min), 7=Parodontologie(120min), 8=Urgences(60min)

-- Médecin ID = 3

-- Patient 5 (Jean PATIENT) - 2 rendez-vous
-- RDV 1: 18/10/2025 à 09h00 - Consultation générale (30min)
INSERT INTO rendezvous (creneau_id, patient_id, medecin_id, statut) 
SELECT c.id, 5, 3, 'CONFIRME'
FROM creneau c
WHERE c.agenda_id = 1 
  AND DATE(c.debut) = '2025-10-18' 
  AND TIME(c.debut) = '09:00:00'
  AND c.est_reserve = 0
LIMIT 1;

UPDATE creneau SET est_reserve = 1, service_id = 1 
WHERE agenda_id = 1 AND DATE(debut) = '2025-10-18' AND TIME(debut) = '09:00:00';

-- RDV 2: 25/10/2025 à 14h00 - Détartrage (30min)
INSERT INTO rendezvous (creneau_id, patient_id, medecin_id, statut) 
SELECT c.id, 5, 3, 'CONFIRME'
FROM creneau c
WHERE c.agenda_id = 1 
  AND DATE(c.debut) = '2025-10-25' 
  AND TIME(c.debut) = '14:00:00'
  AND c.est_reserve = 0
LIMIT 1;

UPDATE creneau SET est_reserve = 1, service_id = 2 
WHERE agenda_id = 1 AND DATE(debut) = '2025-10-25' AND TIME(debut) = '14:00:00';

-- Patient 6 (Hugues PATIENT) - 2 rendez-vous
-- RDV 1: 21/10/2025 à 10h30 - Orthodontie (30min)
INSERT INTO rendezvous (creneau_id, patient_id, medecin_id, statut) 
SELECT c.id, 6, 3, 'CONFIRME'
FROM creneau c
WHERE c.agenda_id = 1 
  AND DATE(c.debut) = '2025-10-21' 
  AND TIME(c.debut) = '10:30:00'
  AND c.est_reserve = 0
LIMIT 1;

UPDATE creneau SET est_reserve = 1, service_id = 4 
WHERE agenda_id = 1 AND DATE(debut) = '2025-10-21' AND TIME(debut) = '10:30:00';

-- RDV 2: 28/10/2025 à 15h30 - Consultation générale (30min)
INSERT INTO rendezvous (creneau_id, patient_id, medecin_id, statut) 
SELECT c.id, 6, 3, 'CONFIRME'
FROM creneau c
WHERE c.agenda_id = 1 
  AND DATE(c.debut) = '2025-10-28' 
  AND TIME(c.debut) = '15:30:00'
  AND c.est_reserve = 0
LIMIT 1;

UPDATE creneau SET est_reserve = 1, service_id = 1 
WHERE agenda_id = 1 AND DATE(debut) = '2025-10-28' AND TIME(debut) = '15:30:00';

-- Patient 7 (Sophie Martin) - 2 rendez-vous
-- RDV 1: 19/10/2025 à 08h30 - Urgences dentaires (60min = 2 créneaux)
INSERT INTO rendezvous (creneau_id, patient_id, medecin_id, statut) 
SELECT c.id, 7, 3, 'CONFIRME'
FROM creneau c
WHERE c.agenda_id = 1 
  AND DATE(c.debut) = '2025-10-19' 
  AND TIME(c.debut) = '08:30:00'
  AND c.est_reserve = 0
LIMIT 1;

UPDATE creneau SET est_reserve = 1, service_id = 8 
WHERE agenda_id = 1 AND DATE(debut) = '2025-10-19' AND TIME(debut) BETWEEN '08:30:00' AND '09:00:00';

-- RDV 2: 27/10/2025 à 16h00 - Détartrage (30min)
INSERT INTO rendezvous (creneau_id, patient_id, medecin_id, statut) 
SELECT c.id, 7, 3, 'CONFIRME'
FROM creneau c
WHERE c.agenda_id = 1 
  AND DATE(c.debut) = '2025-10-27' 
  AND TIME(c.debut) = '16:00:00'
  AND c.est_reserve = 0
LIMIT 1;

UPDATE creneau SET est_reserve = 1, service_id = 2 
WHERE agenda_id = 1 AND DATE(debut) = '2025-10-27' AND TIME(debut) = '16:00:00';

-- Patient 8 (Jean Dupont) - 2 rendez-vous
-- RDV 1: 20/10/2025 à 09h30 - Consultation générale (30min)
INSERT INTO rendezvous (creneau_id, patient_id, medecin_id, statut) 
SELECT c.id, 8, 3, 'CONFIRME'
FROM creneau c
WHERE c.agenda_id = 1 
  AND DATE(c.debut) = '2025-10-20' 
  AND TIME(c.debut) = '09:30:00'
  AND c.est_reserve = 0
LIMIT 1;

UPDATE creneau SET est_reserve = 1, service_id = 1 
WHERE agenda_id = 1 AND DATE(debut) = '2025-10-20' AND TIME(debut) = '09:30:00';

-- RDV 2: 29/10/2025 à 10h00 - Orthodontie (30min)
INSERT INTO rendezvous (creneau_id, patient_id, medecin_id, statut) 
SELECT c.id, 8, 3, 'CONFIRME'
FROM creneau c
WHERE c.agenda_id = 1 
  AND DATE(c.debut) = '2025-10-29' 
  AND TIME(c.debut) = '10:00:00'
  AND c.est_reserve = 0
LIMIT 1;

UPDATE creneau SET est_reserve = 1, service_id = 4 
WHERE agenda_id = 1 AND DATE(debut) = '2025-10-29' AND TIME(debut) = '10:00:00';

-- Patient 9 (Claire Durand) - 2 rendez-vous
-- RDV 1: 22/10/2025 à 14h30 - Parodontologie (120min = 4 créneaux)
INSERT INTO rendezvous (creneau_id, patient_id, medecin_id, statut) 
SELECT c.id, 9, 3, 'CONFIRME'
FROM creneau c
WHERE c.agenda_id = 1 
  AND DATE(c.debut) = '2025-10-22' 
  AND TIME(c.debut) = '14:30:00'
  AND c.est_reserve = 0
LIMIT 1;

UPDATE creneau SET est_reserve = 1, service_id = 7 
WHERE agenda_id = 1 AND DATE(debut) = '2025-10-22' AND TIME(debut) BETWEEN '14:30:00' AND '16:00:00';

-- RDV 2: 30/10/2025 à 11h00 - Détartrage (30min)
INSERT INTO rendezvous (creneau_id, patient_id, medecin_id, statut) 
SELECT c.id, 9, 3, 'CONFIRME'
FROM creneau c
WHERE c.agenda_id = 1 
  AND DATE(c.debut) = '2025-10-30' 
  AND TIME(c.debut) = '11:00:00'
  AND c.est_reserve = 0
LIMIT 1;

UPDATE creneau SET est_reserve = 1, service_id = 2 
WHERE agenda_id = 1 AND DATE(debut) = '2025-10-30' AND TIME(debut) = '11:00:00';

-- Patient 10 (Luc Petit) - 2 rendez-vous
-- RDV 1: 18/10/2025 à 11h30 - Consultation générale (30min)
INSERT INTO rendezvous (creneau_id, patient_id, medecin_id, statut) 
SELECT c.id, 10, 3, 'DEMANDE'
FROM creneau c
WHERE c.agenda_id = 1 
  AND DATE(c.debut) = '2025-10-18' 
  AND TIME(c.debut) = '11:30:00'
  AND c.est_reserve = 0
LIMIT 1;

UPDATE creneau SET est_reserve = 1, service_id = 1 
WHERE agenda_id = 1 AND DATE(debut) = '2025-10-18' AND TIME(debut) = '11:30:00';

-- RDV 2: 24/10/2025 à 16h30 - Orthodontie (30min)
INSERT INTO rendezvous (creneau_id, patient_id, medecin_id, statut) 
SELECT c.id, 10, 3, 'CONFIRME'
FROM creneau c
WHERE c.agenda_id = 1 
  AND DATE(c.debut) = '2025-10-24' 
  AND TIME(c.debut) = '16:30:00'
  AND c.est_reserve = 0
LIMIT 1;

UPDATE creneau SET est_reserve = 1, service_id = 4 
WHERE agenda_id = 1 AND DATE(debut) = '2025-10-24' AND TIME(debut) = '16:30:00';

-- Patient 11 (Emma Lefevre) - 2 rendez-vous
-- RDV 1: 21/10/2025 à 15h00 - Implantologie (90min = 3 créneaux)
INSERT INTO rendezvous (creneau_id, patient_id, medecin_id, statut) 
SELECT c.id, 11, 3, 'CONFIRME'
FROM creneau c
WHERE c.agenda_id = 1 
  AND DATE(c.debut) = '2025-10-21' 
  AND TIME(c.debut) = '15:00:00'
  AND c.est_reserve = 0
LIMIT 1;

UPDATE creneau SET est_reserve = 1, service_id = 3 
WHERE agenda_id = 1 AND DATE(debut) = '2025-10-21' AND TIME(debut) BETWEEN '15:00:00' AND '16:00:00';

-- RDV 2: 26/10/2025 à 09h00 - Détartrage (30min)
INSERT INTO rendezvous (creneau_id, patient_id, medecin_id, statut) 
SELECT c.id, 11, 3, 'CONFIRME'
FROM creneau c
WHERE c.agenda_id = 1 
  AND DATE(c.debut) = '2025-10-26' 
  AND TIME(c.debut) = '09:00:00'
  AND c.est_reserve = 0
LIMIT 1;

UPDATE creneau SET est_reserve = 1, service_id = 2 
WHERE agenda_id = 1 AND DATE(debut) = '2025-10-26' AND TIME(debut) = '09:00:00';

-- Patient 12 (Paul Moreau) - 2 rendez-vous
-- RDV 1: 23/10/2025 à 08h00 - Urgences dentaires (60min = 2 créneaux)
INSERT INTO rendezvous (creneau_id, patient_id, medecin_id, statut) 
SELECT c.id, 12, 3, 'CONFIRME'
FROM creneau c
WHERE c.agenda_id = 1 
  AND DATE(c.debut) = '2025-10-23' 
  AND TIME(c.debut) = '08:00:00'
  AND c.est_reserve = 0
LIMIT 1;

UPDATE creneau SET est_reserve = 1, service_id = 8 
WHERE agenda_id = 1 AND DATE(debut) = '2025-10-23' AND TIME(debut) BETWEEN '08:00:00' AND '08:30:00';

-- RDV 2: 30/10/2025 à 14h30 - Consultation générale (30min)
INSERT INTO rendezvous (creneau_id, patient_id, medecin_id, statut) 
SELECT c.id, 12, 3, 'DEMANDE'
FROM creneau c
WHERE c.agenda_id = 1 
  AND DATE(c.debut) = '2025-10-30' 
  AND TIME(c.debut) = '14:30:00'
  AND c.est_reserve = 0
LIMIT 1;

UPDATE creneau SET est_reserve = 1, service_id = 1 
WHERE agenda_id = 1 AND DATE(debut) = '2025-10-30' AND TIME(debut) = '14:30:00';

-- Patient 13 (Julie Girard) - 2 rendez-vous
-- RDV 1: 19/10/2025 à 10h00 - Consultation générale (30min)
INSERT INTO rendezvous (creneau_id, patient_id, medecin_id, statut) 
SELECT c.id, 13, 3, 'CONFIRME'
FROM creneau c
WHERE c.agenda_id = 1 
  AND DATE(c.debut) = '2025-10-19' 
  AND TIME(c.debut) = '10:00:00'
  AND c.est_reserve = 0
LIMIT 1;

UPDATE creneau SET est_reserve = 1, service_id = 1 
WHERE agenda_id = 1 AND DATE(debut) = '2025-10-19' AND TIME(debut) = '10:00:00';

-- RDV 2: 27/10/2025 à 11h30 - Orthodontie (30min)
INSERT INTO rendezvous (creneau_id, patient_id, medecin_id, statut) 
SELECT c.id, 13, 3, 'CONFIRME'
FROM creneau c
WHERE c.agenda_id = 1 
  AND DATE(c.debut) = '2025-10-27' 
  AND TIME(c.debut) = '11:30:00'
  AND c.est_reserve = 0
LIMIT 1;

UPDATE creneau SET est_reserve = 1, service_id = 4 
WHERE agenda_id = 1 AND DATE(debut) = '2025-10-27' AND TIME(debut) = '11:30:00';

-- Patient 14 (Pierre Roux) - 2 rendez-vous
-- RDV 1: 20/10/2025 à 14h00 - Détartrage (30min)
INSERT INTO rendezvous (creneau_id, patient_id, medecin_id, statut) 
SELECT c.id, 14, 3, 'CONFIRME'
FROM creneau c
WHERE c.agenda_id = 1 
  AND DATE(c.debut) = '2025-10-20' 
  AND TIME(c.debut) = '14:00:00'
  AND c.est_reserve = 0
LIMIT 1;

UPDATE creneau SET est_reserve = 1, service_id = 2 
WHERE agenda_id = 1 AND DATE(debut) = '2025-10-20' AND TIME(debut) = '14:00:00';

-- RDV 2: 28/10/2025 à 10h30 - Consultation générale (30min)
INSERT INTO rendezvous (creneau_id, patient_id, medecin_id, statut) 
SELECT c.id, 14, 3, 'CONFIRME'
FROM creneau c
WHERE c.agenda_id = 1 
  AND DATE(c.debut) = '2025-10-28' 
  AND TIME(c.debut) = '10:30:00'
  AND c.est_reserve = 0
LIMIT 1;

UPDATE creneau SET est_reserve = 1, service_id = 1 
WHERE agenda_id = 1 AND DATE(debut) = '2025-10-28' AND TIME(debut) = '10:30:00';

-- Patient 15 (Laura Blanc) - 2 rendez-vous
-- RDV 1: 22/10/2025 à 09h00 - Orthodontie (30min)
INSERT INTO rendezvous (creneau_id, patient_id, medecin_id, statut) 
SELECT c.id, 15, 3, 'CONFIRME'
FROM creneau c
WHERE c.agenda_id = 1 
  AND DATE(c.debut) = '2025-10-22' 
  AND TIME(c.debut) = '09:00:00'
  AND c.est_reserve = 0
LIMIT 1;

UPDATE creneau SET est_reserve = 1, service_id = 4 
WHERE agenda_id = 1 AND DATE(debut) = '2025-10-22' AND TIME(debut) = '09:00:00';

-- RDV 2: 29/10/2025 à 15h00 - Détartrage (30min)
INSERT INTO rendezvous (creneau_id, patient_id, medecin_id, statut) 
SELECT c.id, 15, 3, 'CONFIRME'
FROM creneau c
WHERE c.agenda_id = 1 
  AND DATE(c.debut) = '2025-10-29' 
  AND TIME(c.debut) = '15:00:00'
  AND c.est_reserve = 0
LIMIT 1;

UPDATE creneau SET est_reserve = 1, service_id = 2 
WHERE agenda_id = 1 AND DATE(debut) = '2025-10-29' AND TIME(debut) = '15:00:00';

-- Patient 16 (Antoine Faure) - 2 rendez-vous
-- RDV 1: 23/10/2025 à 16h00 - Consultation générale (30min)
INSERT INTO rendezvous (creneau_id, patient_id, medecin_id, statut) 
SELECT c.id, 16, 3, 'CONFIRME'
FROM creneau c
WHERE c.agenda_id = 1 
  AND DATE(c.debut) = '2025-10-23' 
  AND TIME(c.debut) = '16:00:00'
  AND c.est_reserve = 0
LIMIT 1;

UPDATE creneau SET est_reserve = 1, service_id = 1 
WHERE agenda_id = 1 AND DATE(debut) = '2025-10-23' AND TIME(debut) = '16:00:00';

-- RDV 2: 25/10/2025 à 08h30 - Urgences dentaires (60min = 2 créneaux)
INSERT INTO rendezvous (creneau_id, patient_id, medecin_id, statut) 
SELECT c.id, 16, 3, 'CONFIRME'
FROM creneau c
WHERE c.agenda_id = 1 
  AND DATE(c.debut) = '2025-10-25' 
  AND TIME(c.debut) = '08:30:00'
  AND c.est_reserve = 0
LIMIT 1;

UPDATE creneau SET est_reserve = 1, service_id = 8 
WHERE agenda_id = 1 AND DATE(debut) = '2025-10-25' AND TIME(debut) BETWEEN '08:30:00' AND '09:00:00';

-- Résumé : 24 rendez-vous créés (2 par patient)
-- Patients : 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16
-- Période : du 18/10/2025 au 30/10/2025
-- Services variés : Consultation, Détartrage, Orthodontie, Urgences, Parodontologie, Implantologie
-- Statuts : CONFIRME (majorité) et DEMANDE (quelques-uns)
