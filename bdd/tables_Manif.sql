DROP TABLE  IF EXISTS panier,commande, manifestant, typeManifestants, client;

CREATE TABLE IF NOT EXISTS typeManifestants (
  id int(10) NOT NULL,
  libelle varchar(50) DEFAULT NULL,
  PRIMARY KEY (id)
)  DEFAULT CHARSET=utf8;
-- Contenu de la table typePartisans
INSERT INTO typeManifestants (id, libelle) VALUES
(1, 'Inoffensif'),
(2, 'Dangeureux'),
(3, 'Spécial');

CREATE TABLE IF NOT EXISTS manifestant (
  id int(10) NOT NULL AUTO_INCREMENT,
  typeManifestant int(10) DEFAULT NULL,
  nom varchar(50) DEFAULT NULL,
  description VARCHAR(500) DEFAULT NULL,
  prix float(6,2) DEFAULT NULL,
  photo varchar(50) DEFAULT NULL,
  stock int(11) NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (typeManifestant) REFERENCES typeManifestants (id)
) DEFAULT CHARSET=utf8 ;

INSERT INTO manifestant (id,typeManifestant,nom,prix,photo,stock, description) VALUES
(1,2, 'Nazi','100','nazi.jpg',5, 'Nostalgique du logo rouge, noir et blanc, vous trouverez votre bonheur en louant les services de nos délicieux fans de l\'histoire de la Seconde Guerre Mondiale'),
(4,2, 'Lot de 5x Nazis','350','nazis.jpg',1, 'Nostalgique du logo rouge, noir et blanc, vous trouverez votre bonheur en louant les services de nos délicieux fans de l\'histoire de la Seconde Guerre Mondiale. Maintenant disponible en lot !'),
(2,1, 'Femen','5.5','femen.jpg',4, 'Les droits des femmes doivent être défendu mais être payé c\'est bien aussi alors participez en louant leur service'),
(3,1, 'Pacifiste','8.5','pacifiste.jpg',0, 'Peace & Love');

CREATE TABLE IF NOT EXISTS client (
  id int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  email VARCHAR(50),
  mdp VARCHAR(32),
  nom VARCHAR(25),
  prenom VARCHAR(25),
  droits ENUM ('admin', 'user')
) DEFAULT CHARSET=utf8 ;

INSERT INTO client (email, mdp, nom, prenom, droits) VALUES
  ('pascal.phelipot@gmail.com', '098f6bcd4621d373cade4e832627b4f6', 'Pascal', 'PHELIPOT', 'admin'),
  ('ralijaona.tiona@gmail.com', '098f6bcd4621d373cade4e832627b4f6', 'Tiona', 'RALIJAONA', 'user');


CREATE TABLE commande(
  idCommande int(10) AUTO_INCREMENT NOT NULL,
  idClient int(10) NOT NULL,
  date TIMESTAMP NOT NULL DEFAULT current_timestamp,
  etat ENUM ('waiting', 'sold', 'send') DEFAULT 'waiting',
  total FLOAT(5, 2),
  PRIMARY KEY (idCommande),
  FOREIGN KEY (idClient) REFERENCES client(id)
)DEFAULT CHARSET=utf8;

CREATE TABLE panier(
  idClient int(10),
  quantite int(5),
  idManifestant int(10),
  idCommande int(10) DEFAULT -1,
  PRIMARY KEY (idClient, idManifestant, idCommande),
  FOREIGN KEY (idClient) REFERENCES client(id),
  FOREIGN KEY (idManifestant) REFERENCES manifestant(id)
) DEFAULT CHARSET=utf8;


