create database accueil;
\c accueil

create table if not exists service(
    id serial primary key,
    nom varchar(50) not null,
    email varchar(50) not null,
    mot_de_passe varchar(255) not null,
    telephone varchar(50) not null
);

create table if not exists visiteur(
    id serial primary key,
    nom varchar(50) not null,
    prenom varchar(50) not null,
    cin varchar(20) unique not null,
    email varchar(50) unique not null,
    telephone varchar(50)
);

create table if not exists visiteur_service(
    motif_visite text,
    statut int not null,
    date_heure_arrivee timestamp not null,

    -- id_demande serial primary key,

    id_service int references service(id),
    id_visiteur int references visiteur(id)
);

-- create table if not exists demande_fichier(
--     id_demande int references visiteur_service(id_demande),
--     fichier varchar(255) not null
-- );

create table if not exists ticket(
    id serial primary key,
    temps_estime time not null,
    date date,
    heure_prevu time,
    heure_validation time,

    id_service int references service(id),
    id_visiteur int references visiteur(id)
);

create table if not exists rdv(
    id serial primary key,
    date_heure timestamp not null,
    motif text,

    id_service int references service(id),
    id_visiteur int references visiteur(id)
);

create table jour(
    id serial primary key,
    nom varchar(20) not null
);

insert into jour values 
    (default, 'Lundi'),
    (default, 'Mardi'),
    (default, 'Mercredi'),
    (default, 'Jeudi'),
    (default, 'Vendredi');

create table if not exists creneau_service(
    id serial primary key,
    heure time not null,

    jour int references jour(id),
    id_service int references service(id)
);

create table if not exists appel_offre(
    id serial primary key,
    titre varchar(255) not null,
    description text not null,
    date_lancement date not null,
    date_limite date not null,
    budget_estime numeric(10,2),
    status int not null
);

create table if not exists actionaire(
    id serial primary key,
    nom varchar(100),
    email varchar(50) not null,
    mot_de_passe varchar(255) not null
);

create table if not exists soumission(
    id serial primary key,
    montant_propose numeric(10,2) not null,
    description text,
    status int not null,

    id_soumissionaire int references actionaire(id),
    id_appel_offre int references appel_offre(id)
);