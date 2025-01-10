select
    *
from
    appel_offre_donnees as aod
join
    appel_offre_table as aot
    on aot.id = aod.id_appel_offre
join
    appel_offre_champs as aof
    on aof.id = aod.id_appel_offre_champs
where id_reference = 10;
