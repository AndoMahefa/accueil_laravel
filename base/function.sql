create or replace function clear()
returns void as $$
begin
    truncate table service cascade;
    truncate table appel_offre_table cascade;
    truncate table appel_offre_champs cascade;

    alter sequence service_id_seq restart with 1;
    alter sequence rdv_id_seq restart with 1;
    alter sequence creneau_service_id_seq restart with 1;
    alter sequence role_service_id_seq restart with 1;
    alter sequence employe_id_seq restart with 1;
    alter sequence utilisateur_id_seq restart with 1;
    alter sequence appel_offre_table_id_seq restart with 1;
    alter sequence appel_offre_donnees_id_seq restart with 1;
    alter sequence appel_offre_champs_id_seq restart with 1;

    select insert_service();

    select insert_specifique_role_service();

    select insert_role_service();
end
$$ language plpgsql;


CREATE OR REPLACE FUNCTION insert_specifique_role_service()
RETURNS VOID AS $$
BEGIN
    insert into role_service (role, id_service) values
    -- Accueil
    ('Enregistrer un visiteur', 2),
    ('Modifier un visiteur', 2),
    ('Supprimer un visiteur', 2),
    ('Visualiser tous les visiteurs', 2),
    ('Demander un service', 2),
    ('Visualisation file d''attente par service', 2),
    -- PRMP
    ('Enregistrer un appel d''offre', 5),
    ('Modifier un appel d''offre', 5),
    ('Supprimer un appel d''offre', 5),
    ('Visualiser tous les appels d''offre', 5),
    ('Ajouter un champ pour un appel d''offre', 5),
    ('Modifier un champ pour un appel d''offre', 5),
    ('Supprimer un champ pour un appel d''offre', 5),
    ('Visualiser tous les champs pour un appel d''offre', 5),
    ('Creer un reference', 5),
    ('Liste de tous les references', 5);
END
$$ language plpgsql;


-- CREATE OR REPLACE FUNCTION insert_service()
-- RETURNS VOID AS $$
-- BEGIN
--     insert into service values
--     (default, 'Technique'),
--     (default, 'Accueil'),
--     (default, 'Ressource Humaine'),
--     (default, 'Directeur General'),
--     (default, 'PRMP'),
--     (default, 'Daf');
-- END
-- $$ language plpgsql;


CREATE OR REPLACE FUNCTION insert_role_service()
RETURNS VOID AS $$
DECLARE
    service_record RECORD;
BEGIN
    FOR service_record IN
        SELECT id FROM service
    LOOP
        -- Exclure l'accueil
        IF service_record.id != (SELECT id FROM service WHERE nom = 'Accueil') THEN
            INSERT INTO role_service (id, role, id_service)
            VALUES
                (DEFAULT, 'Demande des visiteurs', service_record.id),
                (DEFAULT, 'Generer ticket', service_record.id),
                (DEFAULT, 'Refuser une demande d''un visiteur', service_record.id),
                (DEFAULT, 'Visualiser file d''attente', service_record.id),
                (DEFAULT, 'Enregistrer les creneaux horaires', service_record.id),
                (DEFAULT, 'Supprimer les creneaux horaires', service_record.id);
        END IF;
    END LOOP;
END
$$ LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION insert_one_service()
RETURNS TRIGGER AS $$
BEGIN
    INSERT INTO role_service (id, role, id_service)
    VALUES
        (DEFAULT, 'Demande des visiteurs', NEW.id),
        (DEFAULT, 'Generer ticket', NEW.id),
        (DEFAULT, 'Refuser une demande d''un visiteur', NEW.id),
        (DEFAULT, 'Visualiser file d''attente', NEW.id),
        (DEFAULT, 'Enregistrer les creneaux horaires', NEW.id),
        (DEFAULT, 'Supprimer les creneaux horaires', NEW.id);

    RETURN NEW;
END
$$ language plpgsql;


CREATE TRIGGER trigger_insert_role_service
AFTER INSERT ON service
FOR EACH ROW
EXECUTE PROCEDURE insert_one_service();


-- Insertion direction
CREATE OR REPLACE FUNCTION insert_direction()
RETURNS VOID AS $$
BEGIN
    INSERT INTO direction VALUES
        (DEFAULT, 'Direction Generale'),
        (DEFAULT, 'Direction Administrative et Financiere', 1),
        (DEFAULT, 'Direction des Affaires Juridiques', 1),
        (DEFAULT, 'Direction Technique', 1);
END
$$ language plpgsql;
-- Insertion direction


-- Insertion service
CREATE OR REPLACE FUNCTION insert_service()
RETURNS VOID AS $$
BEGIN
    INSERT INTO service (id, nom, id_direction) VALUES
        (DEFAULT, 'Agence Comptable', 1),
        (DEFAULT, 'PRMP', 1),
        (DEFAULT, 'Service administratif et des affaires generales', 2),
        (DEFAULT, 'Service financier', 2),
        (DEFAULT, 'Service des ressources humaines', 2),
        (DEFAULT, 'Service de la legislation et du contentieux', 3),
        (DEFAULT, 'Service du remblai et de la police d''ouvrages', 3),
        (DEFAULT, 'Centre operationnel', 4),
        (DEFAULT, 'Service des etudes et de l''intervention', 4),
        (DEFAULT, 'Service de la maintenance et de l''exploitation', 4);
END
$$ language plpgsql;
-- Insertion service


-- Insertion fonction
CREATE OR REPLACE FUNCTION insert_fonction()
RETURNS VOID AS $$
BEGIN
    INSERT INTO fonction (id, nom, id_service, id_direction) VALUES
        -- Direction Generale
        (DEFAULT, 'Directeur General', null, 1),
        (DEFAULT, 'Assistante de Direction General', null, 1),
        (DEFAULT, 'Secretaire de Direction', null, 1),
        -- -- Agence Comptable
        (DEFAULT, 'Agent Comptable', 1, 1),
        (DEFAULT, 'Comptable Matieres', 1, 1),
        (DEFAULT, 'Regisseur', 1, 1),

        -- Direction Administrative et Financiere
        (DEFAULT, 'Directeur Administratif et Financier', null, 2),
        -- -- Service Administratif et des affaires generales
        (DEFAULT, 'Chef de service administratif', 3, 2),
        (DEFAULT, 'Responsable des ressources humaines', 3, 2),
        (DEFAULT, 'Agent de maintenance', 3, 2),
        (DEFAULT, 'Chauffeur soudeur', 3, 2),
        (DEFAULT, 'Chauffeur', 3, 2),
        (DEFAULT, 'Chauffeur mecanicien', 3, 2),
        (DEFAULT, 'Chauffeur poids lourd et engin', 3, 2),
        (DEFAULT, 'Gardien', 3, 2),
        (DEFAULT, 'Fille de salle', 3, 2),
        -- -- Service financier
        (DEFAULT, 'Chef de service financier', 2, 2),
        (DEFAULT, 'Secretaire comptable', 2, 2),

        -- Direction des affaires juridiques
        (DEFAULT, 'Directeur des affaires juridiques', null, 3),

        -- Direction Technique
        (DEFAULT, 'Directeur Technique', null, 4);
END
$$ language plpgsql;
-- Insertion fonction


-- Insertion observation
CREATE OR REPLACE FUNCTION insert_observation()
RETURNS VOID AS $$
BEGIN
    INSERT INTO observation VALUES
        (DEFAULT, 'Fonctionnaire'),
        (DEFAULT, 'Contrat de droit privé'),
        (DEFAULT, 'Poste vacant'),
        (DEFAULT, 'En cours de régularisation');
END
$$ language plpgsql;
-- Insertion observation


-- Accusé de reception
CREATE MATERIALIZED VIEW mv_accuse_reception AS
SELECT
    ro.id,
    ao.appel_offre,
    s.entreprise,
    ro.date_remise,
    ro.heure_remise
FROM remise_offre ro
JOIN appel_offre_table ao ON ao.id = ro.id_appel_offre
JOIN soumissionaire s ON s.id = ro.id_soumissionaire;


CREATE OR REPLACE FUNCTION refresh_accuse_mv()
RETURNS trigger AS $$
BEGIN
    REFRESH MATERIALIZED VIEW mv_accuse_reception;
    RETURN NULL;
END;
$$ LANGUAGE plpgsql;


CREATE TRIGGER refresh_accuse_mv_trigger
AFTER INSERT OR UPDATE OR DELETE ON remise_offre
FOR EACH STATEMENT
EXECUTE FUNCTION refresh_accuse_mv();
-- Accusé de reception
