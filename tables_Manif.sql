DROP TABLE  IF EXISTS paniers,commandes, partisans, users, typePartisans, etats;

CREATE TABLE IF NOT EXISTS typePartisans (
  id int(10) NOT NULL,
  libelle varchar(50) DEFAULT NULL,
  PRIMARY KEY (id)
)  DEFAULT CHARSET=utf8;
-- Contenu de la table typePartisans
INSERT INTO typePartisans (id, libelle) VALUES
(1, 'Nazi'),
(2, 'Femen'),
(3, 'Pacifiste');
(4, 'Hooligan');

CREATE TABLE IF NOT EXISTS etats (
  id int(11) NOT NULL AUTO_INCREMENT,
  libelle varchar(20) NOT NULL,
  PRIMARY KEY (id)
) DEFAULT CHARSET=utf8 ;
-- Contenu de la table etats
INSERT INTO etats (id, libelle) VALUES
(1, 'A contacter'),
(2, 'Expédié');

CREATE TABLE IF NOT EXISTS partisans (
  id int(10) NOT NULL AUTO_INCREMENT,
  typePartisans_id int(10) DEFAULT NULL,
  nom varchar(50) DEFAULT NULL,
  prix float(6,2) DEFAULT NULL,
  quantites int(10) NOT NULL,
  photo varchar(50) DEFAULT NULL,
  dispo tinyint(4) NOT NULL,
  stock int(11) NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_partisans_typePartisans FOREIGN KEY (typePartisans_id) REFERENCES typePartisans (id)
) DEFAULT CHARSET=utf8 ;

INSERT INTO partisans (id,typePartisans_id,nom,prix,photo,dispo,stock) VALUES
(1,1, 'Nazi','100','Nazi.jpeg',1,5),
(2,1, 'Femen','5.5','Femen.jpeg',1,4),
(3,2, 'Pacifiste 3','8.5','Pacifiste.jpeg',1,10);