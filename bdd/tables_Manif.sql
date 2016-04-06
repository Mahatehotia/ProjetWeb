DROP TABLE  IF EXISTS paniers,commandes, partisans, users, typePartisans, etats, manifestant, typeManifestants;

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
  quantites int(10) NOT NULL,
  photo varchar(50) DEFAULT NULL,
  stock int(11) NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (typeManifestant) REFERENCES typeManifestants (id)
) DEFAULT CHARSET=utf8 ;

INSERT INTO manifestant (id,typeManifestant,nom,prix,photo,stock) VALUES
(1,2, 'Nazi','100','Nazi.jpeg',5),
(2,2, 'Femen','5.5','Femen.jpeg',4),
(3,1, 'Pacifiste 3','8.5','Pacifiste.jpeg',10);