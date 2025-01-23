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


CREATE OR REPLACE FUNCTION insert_service()
RETURNS VOID AS $$
BEGIN
    insert into service values
    (default, 'Technique'),
    (default, 'Accueil'),
    (default, 'Ressource Humaine'),
    (default, 'Directeur General'),
    (default, 'PRMP'),
    (default, 'Daf');
END
$$ language plpgsql;


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
