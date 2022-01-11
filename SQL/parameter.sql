-- Déclaration de la base de donnée 
-- setting
-- Elle contient les datas suivantes
    -- Numéro unique de l'utilisateur (clé étrangère)
    -- Le nombre de jour sur lesquels seront affiché les évènements
    -- La couleur du samedi
    -- La couleur du dimanche
    -- La couleur du jour cliqué
    -- La couleur des jours fériées
    -- La couleur des jours ou au moins un évènement est programmé
    -- La couleur des jours entre les jours fériées et le week-end

CREATE TABLE IF NOT EXISTS parameter (
    PARANUM INT PRIMARY KEY,
    CONSTRAINT fk_parameter_consumer
    FOREIGN KEY (PARANUM)
    REFERENCES consumer(CONSNUM)
    ON DELETE CASCADE,
    -- LE NOMBRE DE JOUR POUR LES AFFICHAGE DES EVENEMENTS
    PARADAYEVE INT NOT NULL DEFAULT 7,
    -- LES COULEURS DE CHAQUE PARTICULARITE
    PARASAT VARCHAR(10) NOT NULL DEFAULT "#EC8889",
    PARASUN VARCHAR(10) NOT NULL DEFAULT "#E40808",
    PARACLOSE VARCHAR(10) NOT NULL DEFAULT "#09F7F7",
    PARAEVEN VARCHAR(10) NOT NULL DEFAULT "#FFFFFF",
    PARADECK VARCHAR(10) NOT NULL DEFAULT "#B4BCC4",
    PARACLICK VARCHAR(10) NOT NULL DEFAULT "#EB930C"
)ENGINE = InnoDB;