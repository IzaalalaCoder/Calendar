-- Création d'une nouvelle base 
CREATE DATABASE IF NOT EXISTS calendar;

-- Si utilisation via le terminal
USE calendar;

-- Déclaration de la base de donnée 
-- users
-- Elle contient les datas suivantes
    -- Numéro unique de l'utilisateur 
    -- Prénom de l'utilisateur
    -- Nom de famille de l'utilisateur
    -- L'adresse email de l'utilisateur
    -- Le pseudo de l'utilisateur
    -- le mot de passe de l'utilisateur

CREATE TABLE IF NOT EXISTS consumer (
    CONSNUM INT AUTO_INCREMENT PRIMARY KEY,
    CONSNAME VARCHAR(100) NOT NULL,
    CONSFAM VARCHAR(100) NOT NULL,
    CONSCOUNTRY VARCHAR(50) NOT NULL,
    CONSEMAIL VARCHAR(200) NOT NULL,
    CONSPSEUDO VARCHAR(30) NOT NULL,
    CONSPASSWORD VARCHAR(200) NOT NULL
)ENGINE = InnoDB;