-- Déclaration de la base de donnée 
-- events
-- Elle contient les datas suivantes
    -- Numéro unique de l'évènement 
        --> qui sera la clé primaire car je pars du 
        -- principe qu'un évènement peut avoir le même titre
    -- Le titre de l'évènement
    -- La description de l'évènement
    -- La locatisation de l'évènement
    -- La date de début
    -- La date de fin
    -- Le numéro qui cible l'utilisateur associé 


CREATE TABLE IF NOT EXISTS events (
    EVENUM INT PRIMARY KEY AUTO_INCREMENT,
    EVETITLE VARCHAR(100) NOT NULL,
    EVEDESC VARCHAR(300) NOT NULL DEFAULT 'INCONNU',
    EVELOC VARCHAR(100) NOT NULL DEFAULT 'INCONNU',
    EVESTART DATE NOT NULL,
    EVEEND DATE NOT NULL,
    EVEHOURSTART TIME NOT NULL,
    EVEHOUREND TIME NOT NULL,
    EVECONS INT NOT NULL,
    CONSTRAINT fk_event_consumer
    FOREIGN KEY (EVECONS)
    REFERENCES consumer(CONSNUM)
    ON DELETE CASCADE
)ENGINE = InnoDB;