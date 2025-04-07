-- Pour que les accents et les caracteres speciaux s'affichent dans un projet windows, il faut faire ceci:
-- Étape 1 : Activer UTF-8 dans les paramètres système
-- Ouvrez Paramètres Windows > Heure et langue > Langue et région.

-- Sous "Paramètres associés", cliquez sur Paramètres de langue administratifs.

-- Dans l’onglet Administratif, cochez :

-- "Utiliser Unicode UTF-8 pour prendre en charge les langues du monde entier".

-- Redémarrez votre PC.

create database accueil_project;
\c accueil_project

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

create table if not exists statut(
    id serial primary key,
    statut varchar(20) not null
);

insert into statut values
    (default, 'Présent'),
    (default, 'Absent'),
    (default, 'Retard'),
    (default, 'Congé'),
    (default, 'Permission');

create table if not exists pointage(
    id serial primary key,
    date date not null default current_date,
    heure_arrivee time not null,
    heure_depart time,
    session int not null,

    id_employe int references employe(id),
    id_statut int references statut(id)
);

create table if not exists utilisateur(
    id serial primary key,
    email varchar(50) unique not null,
    mot_de_passe varchar(255) not null,
    role varchar(50) not null,

    id_employe int references employe(id)
);

create table if not exists fonctionnalite(
    id serial primary key,
    -- nom varchar(255) not null,
    titre varchar(255) not null,
    vers varchar(100) not null,
    icon varchar(75) not null,
    statut int not null,

    id_fonctionnalite int references fonctionnalite(id)
);

create table if not exists role_utilisateur(
    id_fonctionnalite int references fonctionnalite(id),
    id_utilisateur int references utilisateur(id)
);

create table if not exists actions(
    id serial primary key,
    nom varchar(100) not null,

    id_fonctionnalite int references fonctionnalite(id)
);

create table if not exists utilisateur_action(
    id_utilisateur int references utilisateur(id),
    id_action int references actions(id)
);

create table if not exists visiteur(
    id serial primary key,
    nom varchar(50) not null,
    prenom varchar(50) not null,
    cin varchar(20) unique not null,
    email varchar(50) unique,
    telephone varchar(50),
    genre varchar(20) not null,
    entreprise varchar(150)
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
    id_service int references service(id),
    id_visiteur int references visiteur(id)
);

create table if not exists rdv(
    id serial primary key,
    date_heure timestamp not null,
    motif text,
    heure_fin time,

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
    heure_fin time not null,
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
