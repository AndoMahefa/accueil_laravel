-- Vue pour les details d'un appel d'offre
    create or replace view v_appel_details as
    select
        aod.id_appel_offre,
        JSON_AGG(
            JSON_BUILD_OBJECT(
                'id_appel_offre_donnees', aod.id,
                'valeur', aod.valeur,
                'id_appel_offre_champs', aod.id_appel_offre_champs,
                'nom_champ', aoc.nom_champ,
                'type_champ', aoc.type_champ,
                'options', aoc.options
            )
        ) as details
        from appel_offre_donnees as aod
        join appel_offre_champs as aoc on aod.id_appel_offre_champs = aoc.id
        group by id_appel_offre;

-- -- Nombre totals de visiteurs par service :
    create or replace view v_nb_visiteurs_service_sans_rdv as
    select
        s.nom as service,
        count(id_visiteur) as total_visiteurs
    from
        visiteur_service vs
    join service s on vs.id_service = s.id
    group by
        s.nom, vs.id_service;

    create or replace view v_nb_visiteurs_service_avec_rdv as
    select
        s.nom as service,
        count(id_visiteur) as total_visiteurs
    from
        rdv r
    join
        service s on r.id_service = s.id
    group by
        s.nom, r.id_service;

    create or replace view v_nb_visiteurs_service as
    WITH v_nb_visiteurs_service AS (
        select
            *
        from
            v_nb_visiteurs_service_avec_rdv
        union all
        select
            *
        from
            v_nb_visiteurs_service_sans_rdv
    ) select
        service,
        sum(total_visiteurs)::bigint as total_visiteurs
    from v_nb_visiteurs_service
    group by service;

-- -- Nombre totals de visiteurs par direction :
    create or replace view v_nb_visiteurs_direction_sans_rdv as
    select
        s.nom as direction,
        count(id_visiteur) as total_visiteurs
    from
        visiteur_service vs
    join direction s on vs.id_direction = s.id
    group by
        s.nom, vs.id_direction;

    create or replace view v_nb_visiteurs_direction_avec_rdv as
    select
        s.nom as direction,
        count(id_visiteur) as total_visiteurs
    from
        rdv r
    join
        direction s on r.id_direction = s.id
    group by
        s.nom, r.id_direction;

    create or replace view v_nb_visiteurs_direction as
    WITH v_nb_visiteurs_service AS (
        select
            *
        from
            v_nb_visiteurs_direction_avec_rdv
        union all
        select
            *
        from
            v_nb_visiteurs_direction_sans_rdv
    ) select
        direction,
        sum(total_visiteurs)::bigint as total_visiteurs
    from v_nb_visiteurs_service
    group by direction;

-- Vue pour visites quotidiennes/mensuelles (RDV + Sans RDV)
    CREATE OR REPLACE VIEW v_visites_par_periode_avec_visiteurs AS
    -- Visites sans RDV
    SELECT
        v.id AS id_visiteur,
        v.nom,
        v.prenom,
        DATE_TRUNC('day', vs.date_heure_arrivee) AS jour,
        DATE_TRUNC('month', vs.date_heure_arrivee) AS mois
    FROM
        visiteur_service vs
    JOIN
        visiteur v ON vs.id_visiteur = v.id
    UNION ALL
    -- Visites avec RDV
    SELECT
        v.id AS id_visiteur,
        v.nom,
        v.prenom,
        DATE_TRUNC('day', r.date_heure) AS jour,
        DATE_TRUNC('month', r.date_heure) AS mois
    FROM
        rdv r
    JOIN
        visiteur v ON r.id_visiteur = v.id;

    CREATE OR REPLACE VIEW v_visites_par_periode_detail AS
    SELECT
        jour,
        mois,
        COUNT(id_visiteur) AS total_visites,
        -- Liste des visiteurs (agrégés en JSON)
        JSON_AGG(
            JSON_BUILD_OBJECT(
                'id', id_visiteur,
                'nom', nom,
                'prenom', prenom
            )
        ) AS visiteurs
    FROM (
        -- Requête combinée (Version 1)
        SELECT * FROM v_visites_par_periode_avec_visiteurs
    ) AS combined_data
    GROUP BY jour, mois;

-- Vue pour fréquentation des visiteurs
    CREATE OR REPLACE VIEW v_type_visiteurs AS
    WITH visites_par_visiteur AS (
        SELECT
            id_visiteur,
            COUNT(*) AS nb_visites
        FROM (
            SELECT id_visiteur FROM visiteur_service
            UNION ALL
            SELECT id_visiteur FROM rdv
        ) AS all_visites
        GROUP BY id_visiteur
    )
    SELECT
        CASE
            WHEN nb_visites > 1 THEN 'Récurrent'
            ELSE 'Nouveau'
        END AS type_visiteur,
        COUNT(id_visiteur) AS total
    FROM visites_par_visiteur
    GROUP BY type_visiteur;

-- Vue comparative RDV vs Sans RDV
    CREATE OR REPLACE VIEW v_comparaison_rdv_sans_rdv AS
    SELECT
        'Avec RDV' AS type,
        COUNT(id_visiteur) AS total
    FROM rdv
    UNION ALL
    SELECT
        'Sans RDV' AS type,
        COUNT(id_visiteur)
    FROM visiteur_service;
