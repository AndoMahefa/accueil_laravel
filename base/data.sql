create or replace function insert_fonctionnalite()
returns void as $$
begin
    insert into fonctionnalite (id,titre,vers,icon,statut,id_fonctionnalite) values
    -- insert into fonctionnalite values
        (default, 'Tableau de bord', '/home/dashboard', 'mdi-view-dashboard', 0, null),
        (default, 'Organigramme', '/home/organigramme', 'mdi-sitemap', 0, null),

        -- (default, 'Services', '', 'mdi-domain', 0, null),
        -- (default, 'Ajouter un service', '/home/ajouter-service', '', 0, 3),
        -- (default, 'Liste des services', '/home/liste-services', '', 0, 3),

        (default, 'Employés', '', 'mdi-account-group', 0, null),
        (default, 'Ajouter un employé', '/home/ajouter-employe', '', 0, 3),
        (default, 'Liste des employés', '/home/liste-employes', '', 0, 3),
        (default, 'Pointage', '/home/pointage', '', 0, 3),
        (default, 'Liste des pointages', '/home/liste-pointages', '', 0, 3),

        (default, 'Visiteurs', '', 'mdi-account-group', 0, null),
        (default, 'Ajouter un visiteur', '/home/enregistrer-visiteur', '', 0, 8),
        (default, 'Liste des visiteurs', '/home/liste-visiteurs', '', 0, 8),

        (default, 'Appel d''offre', '', 'mdi-file-document', 0, null),
        (default, 'Ajout d''un appel d''offre', '/home/save-reference', '', 0, 11),
        (default, 'Remise d''offre', '/home/remise-offre', '', 0, 11),
        (default, 'Liste soumissionaire', '/home/liste-soumissionaire', '', 0, 11),

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


INSERT INTO visiteur (nom, prenom, cin, email, telephone, genre, entreprise) VALUES
('Randrianarisoa', 'Hery', '101010101010', 'hery.r@exemple.mg', '0321234567', 'Homme', 'Star'),
('Ramanantsoa', 'Fara', '201020304050', 'fara.r@exemple.mg', '0339876543', 'Femme', 'Telma'),
('Rakotomalala', 'Tojo', '303040506070', NULL, '0341112233', 'Homme', 'Orange'),
('Rasolonirina', 'Mamy', '404050607080', 'mamy.r@exemple.mg', '0322233445', 'Homme', 'Jovenna'),
('Andriamasinoro', 'Voahirana', '505060708090', 'voahirana.a@exemple.mg', '0334455667', 'Femme', NULL),
('Razafindrakoto', 'Tiana', '606070809010', NULL, '0345566778', 'Homme', 'SBM'),
('Ratsimbazafy', 'Malala', '707080901020', 'malala.r@exemple.mg', '0326677889', 'Femme', 'BMOI'),
('Rakotomanga', 'Joel', '808090102030', 'joel.r@exemple.mg', '0337788990', 'Homme', NULL),
('Andriamanalina', 'Sarobidy', '909010203040', NULL, '0348899001', 'Femme', 'Airtel'),
('Randriamampionona', 'Fetra', '010203040506', 'fetra.r@exemple.mg', '0329900112', 'Homme', 'Air Madagascar'),
('Rajoelina', 'Niry', '110203040506', 'niry.r@exemple.mg', '0331011121', 'Femme', 'Bionexx'),
('Rakotobe', 'Lova', '120304050607', NULL, '0342122232', 'Homme', NULL),
('Ratsiraka', 'Nomena', '130405060708', 'nomena.r@exemple.mg', '0323233343', 'Femme', 'FJKM'),
('Andrianantenaina', 'Herisoa', '140506070809', 'herisoa.a@exemple.mg', '0334344454', 'Femme', 'UNICEF'),
('Rabe', 'Tsito', '150607080910', NULL, '0345455565', 'Homme', 'CNAPS'),
('Raharinirina', 'Soa', '160708091011', 'soa.r@exemple.mg', '0326566676', 'Femme', 'BOA'),
('Rakotoarisoa', 'Lala', '170809101112', NULL, '0337677787', 'Femme', 'Hasina Construction'),
('Razanajatovo', 'Hanta', '180910111213', 'hanta.r@exemple.mg', '0348788898', 'Femme', 'Ambatovy'),
('Rafalimanana', 'Faly', '190101112131', 'faly.r@exemple.mg', '0329899909', 'Homme', NULL),
('Andrianarivelo', 'Niry', '200202122232', NULL, '0330909090', 'Homme', 'SBM'),
('Rakotoniaina', 'Zo', '210303133343', 'zo.r@exemple.mg', '0341234561', 'Homme', 'ENI'),
('Razanaparany', 'Sitraka', '220404144454', NULL, '0322345672', 'Homme', NULL),
('Randrianasolo', 'Mialy', '230505155565', 'mialy.r@exemple.mg', '0333456783', 'Femme', 'BFV-SG'),
('Ravelojaona', 'Aina', '240606166676', 'aina.r@exemple.mg', '0344567894', 'Homme', 'Koloina SARL'),
('Andriambaventy', 'Tahina', '250707177787', NULL, '0325678905', 'Homme', 'Viseo');

-- Total : 25 visiteurs
-- 15 ont un email
-- 17 ont une entreprise


-- create or replace function insert_actions()
-- returns void as $$
-- begin
--     insert into actions values
--         -- Organigramme
--         (default, 'Ajouter une direction', 2),
--         (default, 'Ajouter un service', 2),
--         (default, 'Ajouter une fonction', 2),
--         (default, 'Modifier une direction', 2),
--         (default, 'Supprimer une direction', 2),
--         (default, 'Modifier un service', 2),
--         (default, 'Supprimer un service', 2),

--         (default, 'Modifier un employé', 8),
--         (default, 'Supprimer un employé', 8),
--         (default, 'Attribuer un role à un employé', 8),
--         (default, 'Créer un compte', 8),

--         (default, 'Ajouter une direction', 8),
--         (default, 'Ajouter une direction', 8),

--         (default, 'Ajouter une direction', 2),
--         (default, 'Ajouter une direction', 2),
--         (default, 'Ajouter une direction', 2),
--         (default, 'Ajouter une direction', 2),
--         (default, 'Ajouter une direction', 2),
-- end
-- $$ language plpgsql;
