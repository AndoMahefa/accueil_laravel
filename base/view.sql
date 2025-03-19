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