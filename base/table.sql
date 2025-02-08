create database accueil;
\c accueil

create table if not exists direction(
    id serial primary key,
    nom varchar(100) not null,
    deleted_at date,

    id_parent_dir int references direction(id)
);

create table if not exists service(
    id serial primary key,
    nom varchar(100) not null,
    deleted_at date,

    id_direction int references direction(id)
);

create table if not exists fonction(
    id serial primary key,
    nom varchar(100) not null,
    deleted_at date,

    id_service int references service(id),
    id_direction int references direction(id)
);

create table if not exists observation(
    id serial primary key,
    observation text not null,
    deleted_at date
);

create table if not exists role_service(
    id serial primary key,
    role varchar(30) not null,

    id_service int references service(id)
);

create table if not exists employe(
    id serial primary key,
    nom varchar(100) not null,
    prenom varchar(100) not null,
    date_de_naissance date not null,
    adresse varchar(75) not null,
    cin varchar(25) unique not null,
    telephone varchar(25) unique not null,
    genre varchar(20) not null,
    deleted_at date,

    id_direction int references direction(id),
    id_service int references service(id),
    id_fonction int references fonction(id),
    id_observation int references observation(id)
);

insert into employe values
(default, 'RAZAFIMAHATRATRA', 'Ando', '2000-07-11', 'ITD 26 bis Ambaniala Itaosy', '0344773452', 'Homme', '7', );

create table if not exists role_employe(
    id_employe int references employe(id),
    id_role int references role_service(id)
);

create table if not exists utilisateur(
    id serial primary key,
    email varchar(50) unique not null,
    mot_de_passe varchar(255) not null,
    role varchar(50) not null,

    id_employe int references employe(id)
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

    id_direction int references direction(id),
    id_service int references service(id),
    id_fonction int references fonction(id),
    id_visiteur int references visiteur(id)
);

create table if not exists ticket(
    id serial primary key,
    temps_estime time not null,
    date date,
    heure_prevu time,
    heure_validation time,

    id_direction int references direction(id),
    -- id_service int references service(id),
    id_visiteur int references visiteur(id)
);

create table if not exists rdv(
    id serial primary key,
    date_heure timestamp not null,
    motif text,

    id_direction int references direction(id),
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

    id_direction int references direction(id),
    id_service int references service(id)
);

create table if not exists intervalle_creneaux(
    id serial primary key,
    intervalle int not null,

    id_direction int references direction(id),
    id_service int references service(id)
);

-- create table if not exists appel_offre(
--     id serial primary key,
--     titre varchar(255) not null,
--     description text not null,
--     date_lancement date not null,
--     date_limite date not null,
--     budget_estime numeric(10,2),
--     status int not null /* 0 ouvert, 1 ferme, 2 soumis */
-- );

-- create table if not exists actionaire(
--     id serial primary key,
--     nom varchar(100),
--     email varchar(50) not null,
--     mot_de_passe varchar(255) not null
-- );

--  insert into actionaire values
--  (default, 'Societe 1', 'societe1@gmail.com', '12345678'),
--  (default,),
--  ();

-- create table if not exists soumission(
--     id serial primary key,
--     montant_propose numeric(10,2) not null,
--     delai int,
--     description text not null,
--     status int not null,

--     id_soumissionaire int references actionaire(id),
--     id_appel_offre int references appel_offre(id)
-- );

create table if not exists reference_ppm(
    id serial primary key,
    reference varchar(25) unique not null
);

create table if not exists appel_offre_table(
    id serial primary key,
    appel_offre varchar(100),
    id_reference int references reference_ppm(id),
    deleted_at date,
    date_publication date,
    date_ouverture_plis date,
    heure_limite time
);

create table if not exists appel_offre_champs(
    id serial primary key,
    nom_champ varchar(100) not null,
    type_champ varchar(50) not null,
    options jsonb
);

create table if not exists appel_offre_donnees(
    id serial primary key,
    valeur text not null,

    id_appel_offre int references appel_offre_table(id),
    id_appel_offre_champs int references appel_offre_champs(id)
);

create table if not exists soumissionaire(
    id serial primary key,
    nom varchar(50) not null,
    prenom varchar(255) not null,
    entreprise varchar(255) not null,
    nif_stat varchar(150) not null,
    adresse_siege varchar(150) not null,
    contact varchar(20) not null,
    rcs varchar(30) not null,
    fiscale varchar(30) not null
);

create table if not exists remise_offre(
    id serial primary key,
    date_remise date not null,
    heure_remise time not null,
    -- montant_propose numeric(10,2) not null,

    id_soumissionaire int references soumissionaire(id),
    id_appel_offre int references appel_offre_table(id)
);
