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

    id_service int references service(id),
    id_visiteur int references visiteur(id)
);

-- create table if not exists demande_visiteur_service(
--     id_visiteur int references visiteur(id),
--     id_service int references service(id),
--     statut int not null
-- );

create table if not exists ticket(
    id serial primary key,
    temps_estime time not null,
    date date,
    heure_prevu time,

    id_service int references service(id),
    id_visiteur int references visiteur(id)
);

create table if not exists rdv(
    id serial primary key,
    date_heure timestamp not null,

    id_service int references service(id),
    id_visiteur int references visiteur(id)
);