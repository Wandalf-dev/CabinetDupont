CREATE TABLE IF NOT EXISTS `rendez_vous` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `patient_id` int(11) NOT NULL,
    `service_id` int(11) NOT NULL,
    `date` date NOT NULL,
    `heure` time NOT NULL,
    `status` enum('CONFIRME','ANNULE','EN_ATTENTE') DEFAULT 'EN_ATTENTE',
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `fk_rendezvous_patient` (`patient_id`),
    KEY `fk_rendezvous_service` (`service_id`),
    CONSTRAINT `fk_rendezvous_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_rendezvous_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;