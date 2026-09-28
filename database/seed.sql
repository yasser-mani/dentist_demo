-- DentaFlow demo data
USE dentaflow;

-- Patients
INSERT INTO patients (id, full_name, phone, insurance, status_id, created_at) VALUES
(1, 'Marie Dubois',      '01 42 86 82 00', 'CPAM Île-de-France',     1, DATE_SUB(NOW(), INTERVAL 45 DAY)),
(2, 'Jean Martin',       '01 48 05 26 26', 'Harmonie Mutuelle',      2, DATE_SUB(NOW(), INTERVAL 30 DAY)),
(3, 'Sophie Laurent',    '06 12 34 56 78', 'MGEN',                   2, DATE_SUB(NOW(), INTERVAL 20 DAY)),
(4, 'Pierre Bernard',    '01 55 12 00 00', NULL,                     1, DATE_SUB(NOW(), INTERVAL 15 DAY)),
(5, 'Isabelle Moreau',   '06 98 76 54 32', 'Mutuelle Générale',      2, DATE_SUB(NOW(), INTERVAL 10 DAY)),
(6, 'Luc Petit',         '01 40 50 60 70', 'AG2R La Mondiale',       1, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(7, 'Camille Roux',      '06 11 22 33 44', 'Malakoff Humanis',       2, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(8, 'Thomas Lefevre',    '01 70 80 90 00', 'MAIF',                   1, NOW());

-- Appointments (relative dates)
INSERT INTO appointments (patient_id, appointment_date, reason, status) VALUES
(1, DATE_ADD(NOW(), INTERVAL  1 HOUR),  'Contrôle annuel',           'scheduled'),
(2, DATE_ADD(NOW(), INTERVAL  3 HOUR),  'Détartrage',                'scheduled'),
(8, DATE_ADD(NOW(), INTERVAL  5 HOUR),  'Consultation initiale',     'scheduled'),
(3, DATE_ADD(NOW(), INTERVAL  1 DAY),   'Soin carie molaire',        'scheduled'),
(4, DATE_ADD(NOW(), INTERVAL  2 DAY),   'Radiographie panoramique',  'scheduled'),
(5, DATE_ADD(NOW(), INTERVAL  3 DAY),   'Couronne sur 16',           'scheduled'),
(6, DATE_ADD(NOW(), INTERVAL  5 DAY),   'Détartrage',                'scheduled'),
(7, DATE_ADD(NOW(), INTERVAL  7 DAY),   'Blanchiment',               'scheduled'),
(2, DATE_SUB(NOW(), INTERVAL  2 DAY),   'Soin sur 36',               'completed'),
(3, DATE_SUB(NOW(), INTERVAL  5 DAY),   'Contrôle',                  'completed'),
(5, DATE_SUB(NOW(), INTERVAL 10 DAY),   'Rendez-vous annulé',        'cancelled');

-- Teeth records (diverse states)
INSERT INTO teeth_records (patient_id, tooth_number, state, treatment, notes, record_date) VALUES
-- Marie Dubois (1): healthy patient, one yellow
(1, 16, 'in_progress', 'Couronne céramique', 'Préparation en cours', DATE_SUB(NOW(), INTERVAL 10 DAY)),

-- Jean Martin (2): multiple treatments
(2, 36, 'treated',      'Résine composite',         'Carie traitée',             DATE_SUB(NOW(), INTERVAL 30 DAY)),
(2, 37, 'needs_intervention', 'Détartrage profond', 'Inflammation gingivale',    DATE_SUB(NOW(), INTERVAL 15 DAY)),
(2, 46, 'in_progress',  'Traitement canalaire',     'Deuxième séance prévue',    DATE_SUB(NOW(), INTERVAL 5 DAY)),

-- Sophie Laurent (3): active treatment
(3, 11, 'treated',      'Facette céramique',        'Pose réussie',              DATE_SUB(NOW(), INTERVAL 60 DAY)),
(3, 21, 'treated',      'Facette céramique',        'Pose réussie',              DATE_SUB(NOW(), INTERVAL 60 DAY)),
(3, 26, 'in_progress',  'Traitement parodontal',    'Nettoyage en profondeur',   DATE_SUB(NOW(), INTERVAL 10 DAY)),
(3, 46, 'needs_intervention', 'Extraction',         'Dent de sagesse incluse',   DATE_SUB(NOW(), INTERVAL 2 DAY)),

-- Pierre Bernard (4): new patient, one issue detected
(4, 17, 'needs_intervention', 'Extraction',         'Dent de sagesse douloureuse', DATE_SUB(NOW(), INTERVAL 5 DAY)),

-- Isabelle Moreau (5): heavy treatment
(5, 16, 'in_progress',  'Couronne métallo-céramique', 'Empreinte prise',         DATE_SUB(NOW(), INTERVAL 8 DAY)),
(5, 15, 'needs_intervention', 'Inlay',              'Carie profonde',            DATE_SUB(NOW(), INTERVAL 8 DAY)),
(5, 25, 'treated',      'Amalgame',                 'Soigné il y a 2 mois',      DATE_SUB(NOW(), INTERVAL 60 DAY)),
(5, 36, 'treated',      'Couronne or',              'Ancienne restauration',     DATE_SUB(NOW(), INTERVAL 365 DAY)),

-- Luc Petit (6): clean, one red
(6, 48, 'needs_intervention', 'Extraction',         'Dent de sagesse semi-incluse', DATE_SUB(NOW(), INTERVAL 3 DAY)),

-- Camille Roux (7): cosmetic work
(7, 11, 'in_progress',  'Blanchiment',              'Première séance effectuée', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(7, 21, 'in_progress',  'Blanchiment',              'Première séance effectuée', DATE_SUB(NOW(), INTERVAL 1 DAY)),

-- Thomas Lefevre (8): new, no records yet
(8, 26, 'needs_intervention', 'Contrôle à prévoir', 'Sensibilité signalée',     NOW());

-- Patient notes
INSERT INTO patient_notes (patient_id, content, created_at) VALUES
(1, 'Patiente très anxieuse. Prévoir anesthésie locale renforcée.',              DATE_SUB(NOW(), INTERVAL 40 DAY)),
(1, 'Couronne 16 : empreinte validée, livraison prévue dans 10 jours.',          DATE_SUB(NOW(), INTERVAL 8 DAY)),
(2, 'Hygiène bucco-dentaire à améliorer. Conseils de brossage donnés.',          DATE_SUB(NOW(), INTERVAL 25 DAY)),
(2, 'Traitement canalaire 46 : irrigation complétée, obturation prochaine.',     DATE_SUB(NOW(), INTERVAL 3 DAY)),
(3, 'Patiente satisfaite des facettes. Souhaite blanchiment complémentaire.',    DATE_SUB(NOW(), INTERVAL 50 DAY)),
(3, 'Dent 46 : extraction planifiée sous anesthésie locale, patient informé.',   DATE_SUB(NOW(), INTERVAL 1 DAY)),
(4, 'Première visite. Radiographie panoramique prescrite.',                      DATE_SUB(NOW(), INTERVAL 15 DAY)),
(5, 'Patiente régulière, bon suivi. Couronne 16 bientôt terminée.',              DATE_SUB(NOW(), INTERVAL 7 DAY)),
(6, 'Patient souhaite éviter extraction si possible. Second avis orthodontique.',DATE_SUB(NOW(), INTERVAL 2 DAY)),
(7, 'Blanchiment : sensibilité légère signalée, gel désensibilisant prescrit.',  DATE_SUB(NOW(), INTERVAL 1 DAY)),
(8, 'Nouveau patient. Antécédents : aucune allergie connue.',                    NOW());