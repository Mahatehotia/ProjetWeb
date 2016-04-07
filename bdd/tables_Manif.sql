DROP TABLE  IF EXISTS paniers,commandes, manifestant, typeManifestants, client;

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
  prix float(6,2) DEFAULT NULL,
  photo varchar(50) DEFAULT NULL,
  stock int(11) NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (typeManifestant) REFERENCES typeManifestants (id)
) DEFAULT CHARSET=utf8 ;

INSERT INTO manifestant (id,typeManifestant,nom,prix,photo,stock) VALUES
(1,2, 'Nazi','100','Nazi.jpeg',5),
(2,1, 'Femen','5.5','Femen.jpeg',4),
(3,1, 'Pacifiste','8.5','Pacifiste.jpeg',10);

CREATE TABLE IF NOT EXISTS client (
  id int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  email VARCHAR(50),
  mdp VARCHAR(32),
  nom VARCHAR(25),
  prenom VARCHAR(25),
  droits ENUM ('admin', 'user')
);

INSERT INTO client (email, mdp, nom, prenom, droits) VALUES
  ('pascal.phelipot@gmail.com', '21232f297a57a5a743894a0e4a801fc3', 'Pascal', 'PHELIPOT', 'admin'),
  ('ralijaona.tiona@gmail.com', '098f6bcd4621d373cade4e832627b4f6', 'Tiona', 'RALIJAONA', 'user');