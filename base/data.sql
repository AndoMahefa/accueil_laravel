create or replace function insert_fonctionnalite()
returns void as $$
begin
    insert into fonctionnalite (id,titre,vers,icon,statut,id_fonctionnalite) values
    -- insert into fonctionnalite values
        (default, 'Tableau de bord', '/home/dashboard', 'mdi-view-dashboard', 0, null),
        (default, 'Organigramme', '/home/organigramme', 'mdi-sitemap', 0, null),

        (default, 'Services', '', 'mdi-domain', 0, null),
        (default, 'Ajouter un service', '/home/ajouter-service', '', 0, 3),
        (default, 'Liste des services', '/home/liste-services', '', 0, 3),

        (default, 'Employés', '', 'mdi-account-group', 0, null),
        (default, 'Ajouter un employé', '/home/ajouter-employe', '', 0, 6),
        (default, 'Liste des employés', '/home/liste-employes', '', 0, 6),
        (default, 'Pointage', '/home/pointage', '', 0, 6),
        (default, 'Liste des pointages', '/home/liste-pointages', '', 0, 6),

        (default, 'Visiteurs', '', 'mdi-account-group', 0, null),
        (default, 'Ajouter un visiteur', '/home/enregistrer-visiteur', '', 0, 11),
        (default, 'Liste des visiteurs', '/home/liste-visiteurs', '', 0, 11),

        (default, 'Appel d''offre', '', 'mdi-file-document', 0, null),
        (default, 'Ajout d''un appel d''offre', '/home/save-reference', '', 0, 14),
        (default, 'Remise d''offre', '/home/remise-offre', '', 0, 14),
        (default, 'Liste soumissionaire', '/home/liste-soumissionaire', '', 0, 14),

        (default, 'Demande Recu', '/home/demande-recu', 'mdi-inbox', 0, null),
        (default, 'File d''attente', '/home/file-attente-service', 'mdi-clock-outline', 0, null),
        (default, 'Disponibilités', '/home/jour-creneaux', 'mdi-calendar-clock', 0, null),
        (default, 'Calendrier des rdv', '/home/rendez-vous-service', 'mdi-calendar', 0, null),

        -- admin
        (default, 'Demande Recu', '/home/demande-recu-service', 'mdi-inbox', 1, null),
        (default, 'File d''attente', '/home/file-attente', 'mdi-clock-outline', 1, null),
        (default, 'Disponibilités', '/home/jour-creneaux-service', 'mdi-calendar-clock', 1, null),
        (default, 'Calendrier des rdv ', '/home/rendez-vous', 'mdi-calendar', 1, null);
end
$$ language plpgsql;

create or replace function insert_actions()
returns void as $$
begin
    insert into actions values
        -- Organigramme
        (default, 'Ajouter une direction', 2),
        (default, 'Ajouter un service', 2),
        (default, 'Ajouter une fonction', 2),
        (default, 'Modifier une direction', 2),
        (default, 'Supprimer une direction', 2),
        (default, 'Modifier un service', 2),
        (default, 'Supprimer un service', 2),

        (default, 'Modifier un employé', 8),
        (default, 'Supprimer un employé', 8),
        (default, 'Attribuer un role à un employé', 8),
        (default, 'Créer un compte', 8),
        
        (default, 'Ajouter une direction', 8),
        (default, 'Ajouter une direction', 8),

        (default, 'Ajouter une direction', 2),
        (default, 'Ajouter une direction', 2),
        (default, 'Ajouter une direction', 2),
        (default, 'Ajouter une direction', 2),
        (default, 'Ajouter une direction', 2),
end
$$ language plpgsql;
