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
end
$$ language plpgsql;
