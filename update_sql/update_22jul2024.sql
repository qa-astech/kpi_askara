ALTER TABLE kpi_department_realization
ADD COLUMN IF NOT EXISTS remarks text;

ALTER TABLE kpi_divcorp_realization
ADD COLUMN IF NOT EXISTS remarks text;

DROP VIEW IF EXISTS kpi_bisnis_unit;
DROP VIEW IF EXISTS kpi_department;
DROP VIEW IF EXISTS kpi_divcorp;
DROP VIEW IF EXISTS kpi_realization_dept;
DROP VIEW IF EXISTS mini_kpi_bisnis_unit;
DROP VIEW IF EXISTS mini_kpi_department;
DROP VIEW IF EXISTS mini_kpi_divcorp;
DROP VIEW IF EXISTS target_distinct_kpibunit;
DROP VIEW IF EXISTS target_distinct_kpicorps_dept;
DROP VIEW IF EXISTS target_distinct_kpicorps_divcorp;
DROP VIEW IF EXISTS target_distinct_kpidept;
DROP VIEW IF EXISTS target_distinct_kpidivcorp;

CREATE OR REPLACE VIEW kpi_bisnis_unit
 AS
 SELECT DISTINCT 'kpi_bisnis_unit_corps'::character varying AS status_kpi,
    a.id_kpibunit,
    b.id_kpicorp,
    a.name_kpibunit,
    a.index_kpibunit,
    a.define_kpibunit,
    a.control_cek_kpibunit,
    b.polaritas_kpicorp AS polaritas_kpibunit,
    b.year_kpicorp AS year_kpibunit,
    a.terbit_kpibunit,
    a.cascade_kpibunit,
    a.month_kpibunit,
        CASE
            WHEN a.cascade_kpibunit::text = 'half-round'::text THEN a.target_kpibunit
            ELSE b.target_kpicorp
        END AS target_kpibunit,
    b.target_kpicorp,
    a.id_usersetup AS data_avail_id_usersetup,
    d.fullname_users AS data_avail_fullname_users,
    g.id_department AS data_avail_id_department,
    g.name_department AS data_avail_name_department,
    h.id_company AS compkpi_id,
    h.name_company AS compkpi_name,
    i.id_sobject,
    i.name_sobject,
    i.index_sobject,
    j.id_perspective,
    j.name_perspective,
    j.index_perspective,
    j.alias_perspective,
    k.id_satuan,
    k.name_satuan,
    l.id_formula,
    l.name_formula,
    reverse("substring"(reverse(b.index_kpicorp::text), "position"(reverse(b.index_kpicorp::text), '.'::text) + 1)) AS index_parent,
    (((j.alias_perspective::text || i.index_sobject) || '. '::text) || i.name_sobject)::character varying AS text_sobject,
    ((('('::text || j.alias_perspective::text) || ') '::text) || j.name_perspective)::character varying AS text_perspective,
    a.user_entry,
    a.last_update
   FROM kpi_bisnis_unit_corps a
     LEFT JOIN kpi_corporate b ON b.id_kpicorp::text = a.id_kpicorp::text
     LEFT JOIN users_setup c ON c.id_usersetup::text = a.id_usersetup::text
     LEFT JOIN users d ON d.nik_users::text = c.nik::text
     LEFT JOIN company_detail e ON e.id_det_company::text = c.id_det_company::text
     LEFT JOIN section_master f ON f.id_section::text = e.id_section::text
     LEFT JOIN department_master g ON g.id_department::text = f.id_department::text
     LEFT JOIN company_master h ON h.id_company::text = a.id_company::text
     LEFT JOIN strategic_objective i ON i.id_sobject::text = b.id_sobject::text
     LEFT JOIN perspective j ON j.id_perspective::text = i.id_perspective::text
     LEFT JOIN satuan_master k ON k.id_satuan::text = b.id_satuan::text
     LEFT JOIN formula_master l ON l.id_formula::text = b.id_formula::text
UNION ALL
 SELECT DISTINCT 'kpi_bisnis_unit_support'::character varying AS status_kpi,
    a.id_kpibunit,
    NULL::character varying AS id_kpicorp,
    a.name_kpibunit,
    a.index_kpibunit,
    a.define_kpibunit,
    a.control_cek_kpibunit,
    a.polaritas_kpibunit,
    a.year_kpibunit,
    a.terbit_kpibunit,
    a.cascade_kpibunit,
    a.month_kpibunit,
    a.target_kpibunit,
    NULL::numeric AS target_kpicorp,
    a.id_usersetup AS data_avail_id_usersetup,
    d.fullname_users AS data_avail_fullname_users,
    g.id_department AS data_avail_id_department,
    g.name_department AS data_avail_name_department,
    h.id_company AS compkpi_id,
    h.name_company AS compkpi_name,
    i.id_sobject,
    i.name_sobject,
    i.index_sobject,
    j.id_perspective,
    j.name_perspective,
    j.index_perspective,
    j.alias_perspective,
    k.id_satuan,
    k.name_satuan,
    l.id_formula,
    l.name_formula,
    reverse("substring"(reverse(a.index_kpibunit::text), "position"(reverse(a.index_kpibunit::text), '.'::text) + 1)) AS index_parent,
    (((j.alias_perspective::text || i.index_sobject) || '. '::text) || i.name_sobject)::character varying AS text_sobject,
    ((('('::text || j.alias_perspective::text) || ') '::text) || j.name_perspective)::character varying AS text_perspective,
    a.user_entry,
    a.last_update
   FROM kpi_bisnis_unit_support a
     LEFT JOIN users_setup c ON c.id_usersetup::text = a.id_usersetup::text
     LEFT JOIN users d ON d.nik_users::text = c.nik::text
     LEFT JOIN company_detail e ON e.id_det_company::text = c.id_det_company::text
     LEFT JOIN section_master f ON f.id_section::text = e.id_section::text
     LEFT JOIN department_master g ON g.id_department::text = f.id_department::text
     LEFT JOIN company_master h ON h.id_company::text = a.id_company::text
     LEFT JOIN strategic_objective i ON i.id_sobject::text = a.id_sobject::text
     LEFT JOIN perspective j ON j.id_perspective::text = i.id_perspective::text
     LEFT JOIN satuan_master k ON k.id_satuan::text = a.id_satuan::text
     LEFT JOIN formula_master l ON l.id_formula::text = a.id_formula::text;

CREATE OR REPLACE VIEW kpi_department
 AS
 SELECT DISTINCT 'kpi_department_corps'::character varying AS status_kpi,
    a.id_kpidept,
    b.id_kpibunit,
    a.name_kpidept,
    a.index_kpidept,
    a.define_kpidept,
    a.control_cek_kpidept,
    b.polaritas_kpibunit AS polaritas_kpidept,
    b.year_kpibunit AS year_kpidept,
    a.terbit_kpidept,
    a.cascade_kpidept,
    b.month_kpibunit AS month_kpidept,
    b.target_kpibunit,
    b.target_kpicorp,
    b.data_avail_id_usersetup,
    b.data_avail_fullname_users,
    b.data_avail_id_department,
    b.data_avail_name_department,
    b.compkpi_id,
    b.compkpi_name,
    b.id_sobject,
    b.name_sobject,
    b.index_sobject,
    b.id_perspective,
    b.name_perspective,
    b.index_perspective,
    b.alias_perspective,
    b.id_satuan,
    b.name_satuan,
    b.id_formula,
    b.name_formula,
    b.index_parent,
    b.text_sobject,
    b.text_perspective,
    a.date_cutoff,
    c.id_department AS deptkpi_id,
    c.name_department AS deptkpi_name,
    a.user_entry,
    a.last_update
   FROM kpi_department_corps a
     JOIN kpi_bisnis_unit b ON b.id_kpibunit::text = a.id_kpibunit::text
     LEFT JOIN department_master c ON c.id_department::text = a.id_department::text
UNION ALL
 SELECT DISTINCT 'kpi_department_support'::character varying AS status_kpi,
    a.id_kpidept,
    NULL::character varying AS id_kpibunit,
    a.name_kpidept,
    a.index_kpidept,
    a.define_kpidept,
    a.control_cek_kpidept,
    a.polaritas_kpidept,
    a.year_kpidept,
    a.terbit_kpidept,
    a.cascade_kpidept,
    NULL::text[] AS month_kpidept,
    NULL::numeric AS target_kpibunit,
    NULL::numeric AS target_kpicorp,
    a.id_usersetup AS data_avail_id_usersetup,
    d.fullname_users AS data_avail_fullname_users,
    g.id_department AS data_avail_id_department,
    g.name_department AS data_avail_name_department,
    h.id_company AS compkpi_id,
    h.name_company AS compkpi_name,
    i.id_sobject,
    i.name_sobject,
    i.index_sobject,
    j.id_perspective,
    j.name_perspective,
    j.index_perspective,
    j.alias_perspective,
    k.id_satuan,
    k.name_satuan,
    l.id_formula,
    l.name_formula,
    reverse("substring"(reverse(a.index_kpidept::text), "position"(reverse(a.index_kpidept::text), '.'::text) + 1)) AS index_parent,
    (((j.alias_perspective::text || i.index_sobject) || '. '::text) || i.name_sobject)::character varying AS text_sobject,
    ((('('::text || j.alias_perspective::text) || ') '::text) || j.name_perspective)::character varying AS text_perspective,
    a.date_cutoff,
    m.id_department AS deptkpi_id,
    m.name_department AS deptkpi_name,
    a.user_entry,
    a.last_update
   FROM kpi_department_support a
     JOIN users_setup c ON c.id_usersetup::text = a.id_usersetup::text
     LEFT JOIN users d ON d.nik_users::text = c.nik::text
     LEFT JOIN company_detail e ON e.id_det_company::text = c.id_det_company::text
     LEFT JOIN section_master f ON f.id_section::text = e.id_section::text
     LEFT JOIN department_master g ON g.id_department::text = f.id_department::text
     JOIN company_master h ON h.id_company::text = a.id_company::text
     JOIN strategic_objective i ON i.id_sobject::text = a.id_sobject::text
     LEFT JOIN perspective j ON j.id_perspective::text = i.id_perspective::text
     LEFT JOIN satuan_master k ON k.id_satuan::text = a.id_satuan::text
     LEFT JOIN formula_master l ON l.id_formula::text = a.id_formula::text
     JOIN department_master m ON m.id_department::text = a.id_department::text;

CREATE OR REPLACE VIEW kpi_divcorp
 AS
 SELECT 'kpi_divcorp_corps'::character varying AS status_kpi,
    a.id_kpidivcorp,
    b.id_kpicorp,
    a.name_kpidivcorp,
    a.index_kpidivcorp,
    a.define_kpidivcorp,
    a.control_cek_kpidivcorp,
    b.polaritas_kpicorp AS polaritas_kpidivcorp,
    b.year_kpicorp AS year_kpidivcorp,
    a.date_cutoff,
    a.terbit_kpidivcorp,
    a.cascade_kpidivcorp,
    b.target_kpicorp,
    a.id_usersetup AS data_avail_id_usersetup,
    d.fullname_users AS data_avail_fullname_users,
    g.id_department AS data_avail_id_department,
    g.name_department AS data_avail_name_department,
    h.id_department AS deptkpi_id,
    h.name_department AS deptkpi_name,
    i.id_sobject,
    i.name_sobject,
    i.index_sobject,
    j.id_perspective,
    j.name_perspective,
    j.index_perspective,
    j.alias_perspective,
    k.id_satuan,
    k.name_satuan,
    l.id_formula,
    l.name_formula,
    reverse("substring"(reverse(b.index_kpicorp::text), "position"(reverse(b.index_kpicorp::text), '.'::text) + 1)) AS index_parent,
    (((j.alias_perspective::text || i.index_sobject) || '. '::text) || i.name_sobject)::character varying AS text_sobject,
    ((('('::text || j.alias_perspective::text) || ') '::text) || j.name_perspective)::character varying AS text_perspective,
    a.user_entry,
    a.last_update
   FROM kpi_divcorp_corps a
     LEFT JOIN kpi_corporate b ON b.id_kpicorp::text = a.id_kpicorp::text
     LEFT JOIN users_setup_corps c ON c.id_usersetup::text = a.id_usersetup::text
     LEFT JOIN users d ON d.nik_users::text = c.nik::text
     LEFT JOIN section_master f ON f.id_section::text = c.id_section::text
     LEFT JOIN department_master g ON g.id_department::text = f.id_department::text
     LEFT JOIN department_master h ON h.id_department::text = a.id_department::text
     LEFT JOIN strategic_objective i ON i.id_sobject::text = b.id_sobject::text
     LEFT JOIN perspective j ON j.id_perspective::text = i.id_perspective::text
     LEFT JOIN satuan_master k ON k.id_satuan::text = b.id_satuan::text
     LEFT JOIN formula_master l ON l.id_formula::text = b.id_formula::text
UNION ALL
 SELECT 'kpi_divcorp_support'::character varying AS status_kpi,
    a.id_kpidivcorp,
    NULL::character varying AS id_kpicorp,
    a.name_kpidivcorp,
    a.index_kpidivcorp,
    a.define_kpidivcorp,
    a.control_cek_kpidivcorp,
    a.polaritas_kpidivcorp,
    a.year_kpidivcorp,
    a.date_cutoff,
    a.terbit_kpidivcorp,
    a.cascade_kpidivcorp,
    NULL::numeric AS target_kpicorp,
    a.id_usersetup AS data_avail_id_usersetup,
    d.fullname_users AS data_avail_fullname_users,
    g.id_department AS data_avail_id_department,
    g.name_department AS data_avail_name_department,
    h.id_department AS deptkpi_id,
    h.name_department AS deptkpi_name,
    i.id_sobject,
    i.name_sobject,
    i.index_sobject,
    j.id_perspective,
    j.name_perspective,
    j.index_perspective,
    j.alias_perspective,
    k.id_satuan,
    k.name_satuan,
    l.id_formula,
    l.name_formula,
    reverse("substring"(reverse(a.index_kpidivcorp::text), "position"(reverse(a.index_kpidivcorp::text), '.'::text) + 1)) AS index_parent,
    (((j.alias_perspective::text || i.index_sobject) || '. '::text) || i.name_sobject)::character varying AS text_sobject,
    ((('('::text || j.alias_perspective::text) || ') '::text) || j.name_perspective)::character varying AS text_perspective,
    a.user_entry,
    a.last_update
   FROM kpi_divcorp_support a
     LEFT JOIN users_setup_corps c ON c.id_usersetup::text = a.id_usersetup::text
     LEFT JOIN users d ON d.nik_users::text = c.nik::text
     LEFT JOIN section_master f ON f.id_section::text = c.id_section::text
     LEFT JOIN department_master g ON g.id_department::text = f.id_department::text
     LEFT JOIN department_master h ON h.id_department::text = a.id_department::text
     LEFT JOIN strategic_objective i ON i.id_sobject::text = a.id_sobject::text
     LEFT JOIN perspective j ON j.id_perspective::text = i.id_perspective::text
     LEFT JOIN satuan_master k ON k.id_satuan::text = a.id_satuan::text
     LEFT JOIN formula_master l ON l.id_formula::text = a.id_formula::text;

CREATE OR REPLACE VIEW kpi_realization_dept
 AS
 SELECT DISTINCT 'kpi_divcorp_corps'::character varying AS status_kpi,
    a.id_kpidivcorp AS id_realization,
    a.index_kpidivcorp AS index_kpi_realization,
    a.name_kpidivcorp AS name_kpi_realization,
    a.define_kpidivcorp AS define_kpi_realization,
    a.control_cek_kpidivcorp AS control_cek_kpi_realization,
    b.year_kpicorp AS year_kpi_realization,
    a.date_cutoff,
    b.polaritas_kpicorp AS polaritas_kpi_realization,
    b.target_kpicorp,
    NULL::numeric AS target_kpibunit,
    a.id_usersetup AS data_avail_id_usersetup,
    NULL::character varying AS compkpi_id,
    'Korporat'::character varying AS compkpi_name,
    h.id_department AS deptkpi_id,
    h.name_department AS deptkpi_name,
    i.name_sobject,
    i.index_sobject,
    j.name_perspective,
    j.alias_perspective,
    j.index_perspective,
    k.name_satuan,
    l.name_formula,
    reverse("substring"(reverse(b.index_kpicorp::text), "position"(reverse(b.index_kpicorp::text), '.'::text) + 1)) AS index_parent,
    (((j.alias_perspective::text || i.index_sobject) || '. '::text) || i.name_sobject)::character varying AS text_sobject,
    ((('('::text || j.alias_perspective::text) || ') '::text) || j.name_perspective)::character varying AS text_perspective,
    a.user_entry,
    a.last_update
   FROM kpi_divcorp_corps a
     LEFT JOIN kpi_corporate b ON b.id_kpicorp::text = a.id_kpicorp::text
     LEFT JOIN users_setup_corps c ON c.id_usersetup::text = a.id_usersetup::text
     LEFT JOIN users d ON d.nik_users::text = c.nik::text
     LEFT JOIN section_master f ON f.id_section::text = c.id_section::text
     LEFT JOIN department_master g ON g.id_department::text = f.id_department::text
     LEFT JOIN department_master h ON h.id_department::text = a.id_department::text
     LEFT JOIN strategic_objective i ON i.id_sobject::text = b.id_sobject::text
     LEFT JOIN perspective j ON j.id_perspective::text = i.id_perspective::text
     LEFT JOIN satuan_master k ON k.id_satuan::text = b.id_satuan::text
     LEFT JOIN formula_master l ON l.id_formula::text = b.id_formula::text
  WHERE a.terbit_kpidivcorp IS TRUE
UNION ALL
 SELECT DISTINCT 'kpi_divcorp_support'::character varying AS status_kpi,
    a.id_kpidivcorp AS id_realization,
    a.index_kpidivcorp AS index_kpi_realization,
    a.name_kpidivcorp AS name_kpi_realization,
    a.define_kpidivcorp AS define_kpi_realization,
    a.control_cek_kpidivcorp AS control_cek_kpi_realization,
    a.year_kpidivcorp AS year_kpi_realization,
    a.date_cutoff,
    a.polaritas_kpidivcorp AS polaritas_kpi_realization,
    NULL::numeric AS target_kpicorp,
    NULL::numeric AS target_kpibunit,
    a.id_usersetup AS data_avail_id_usersetup,
    NULL::character varying AS compkpi_id,
    'Korporat'::character varying AS compkpi_name,
    h.id_department AS deptkpi_id,
    h.name_department AS deptkpi_name,
    i.name_sobject,
    i.index_sobject,
    j.name_perspective,
    j.alias_perspective,
    j.index_perspective,
    k.name_satuan,
    l.name_formula,
    reverse("substring"(reverse(a.index_kpidivcorp::text), "position"(reverse(a.index_kpidivcorp::text), '.'::text) + 1)) AS index_parent,
    (((j.alias_perspective::text || i.index_sobject) || '. '::text) || i.name_sobject)::character varying AS text_sobject,
    ((('('::text || j.alias_perspective::text) || ') '::text) || j.name_perspective)::character varying AS text_perspective,
    a.user_entry,
    a.last_update
   FROM kpi_divcorp_support a
     LEFT JOIN users_setup_corps c ON c.id_usersetup::text = a.id_usersetup::text
     LEFT JOIN users d ON d.nik_users::text = c.nik::text
     LEFT JOIN section_master f ON f.id_section::text = c.id_section::text
     LEFT JOIN department_master g ON g.id_department::text = f.id_department::text
     LEFT JOIN department_master h ON h.id_department::text = a.id_department::text
     LEFT JOIN strategic_objective i ON i.id_sobject::text = a.id_sobject::text
     LEFT JOIN perspective j ON j.id_perspective::text = i.id_perspective::text
     LEFT JOIN satuan_master k ON k.id_satuan::text = a.id_satuan::text
     LEFT JOIN formula_master l ON l.id_formula::text = a.id_formula::text
  WHERE a.terbit_kpidivcorp IS TRUE
UNION ALL
 SELECT DISTINCT 'kpi_department_corps'::character varying AS status_kpi,
    a.id_kpidept AS id_realization,
    a.index_kpidept AS index_kpi_realization,
    a.name_kpidept AS name_kpi_realization,
    a.define_kpidept AS define_kpi_realization,
    a.control_cek_kpidept AS control_cek_kpi_realization,
    b.year_kpibunit AS year_kpi_realization,
    a.date_cutoff,
    b.polaritas_kpibunit AS polaritas_kpi_realization,
    b.target_kpicorp,
    b.target_kpibunit,
    b.data_avail_id_usersetup,
    b.compkpi_id,
    b.compkpi_name,
    c.id_department AS deptkpi_id,
    c.name_department AS deptkpi_name,
    b.name_sobject,
    b.index_sobject,
    b.name_perspective,
    b.alias_perspective,
    b.index_perspective,
    b.name_satuan,
    b.name_formula,
    b.index_parent,
    b.text_sobject,
    b.text_perspective,
    a.user_entry,
    a.last_update
   FROM kpi_department_corps a
     JOIN kpi_bisnis_unit b ON b.id_kpibunit::text = a.id_kpibunit::text
     LEFT JOIN department_master c ON c.id_department::text = a.id_department::text
  WHERE a.terbit_kpidept IS TRUE
UNION ALL
 SELECT DISTINCT 'kpi_department_support'::character varying AS status_kpi,
    a.id_kpidept AS id_realization,
    a.index_kpidept AS index_kpi_realization,
    a.name_kpidept AS name_kpi_realization,
    a.define_kpidept AS define_kpi_realization,
    a.control_cek_kpidept AS control_cek_kpi_realization,
    a.year_kpidept AS year_kpi_realization,
    a.date_cutoff,
    a.polaritas_kpidept AS polaritas_kpi_realization,
    NULL::numeric AS target_kpicorp,
    NULL::numeric AS target_kpibunit,
    a.id_usersetup AS data_avail_id_usersetup,
    h.id_company AS compkpi_id,
    h.name_company AS compkpi_name,
    m.id_department AS deptkpi_id,
    m.name_department AS deptkpi_name,
    i.name_sobject,
    i.index_sobject,
    j.name_perspective,
    j.alias_perspective,
    j.index_perspective,
    k.name_satuan,
    l.name_formula,
    reverse("substring"(reverse(a.index_kpidept::text), "position"(reverse(a.index_kpidept::text), '.'::text) + 1)) AS index_parent,
    (((j.alias_perspective::text || i.index_sobject) || '. '::text) || i.name_sobject)::character varying AS text_sobject,
    ((('('::text || j.alias_perspective::text) || ') '::text) || j.name_perspective)::character varying AS text_perspective,
    a.user_entry,
    a.last_update
   FROM kpi_department_support a
     JOIN users_setup c ON c.id_usersetup::text = a.id_usersetup::text
     LEFT JOIN users d ON d.nik_users::text = c.nik::text
     LEFT JOIN company_detail e ON e.id_det_company::text = c.id_det_company::text
     LEFT JOIN section_master f ON f.id_section::text = e.id_section::text
     LEFT JOIN department_master g ON g.id_department::text = f.id_department::text
     JOIN company_master h ON h.id_company::text = a.id_company::text
     JOIN strategic_objective i ON i.id_sobject::text = a.id_sobject::text
     LEFT JOIN perspective j ON j.id_perspective::text = i.id_perspective::text
     LEFT JOIN satuan_master k ON k.id_satuan::text = a.id_satuan::text
     LEFT JOIN formula_master l ON l.id_formula::text = a.id_formula::text
     JOIN department_master m ON m.id_department::text = a.id_department::text
  WHERE a.terbit_kpidept IS TRUE;

CREATE OR REPLACE VIEW mini_kpi_bisnis_unit
 AS
 SELECT DISTINCT tbl.id_kpibunit,
    tbl.id_kpicorp,
    tbl.id_sobject,
    tbl.index_kpibunit,
    tbl.year_kpibunit,
    tbl.compkpi_id
   FROM ( SELECT DISTINCT a.id_kpibunit,
            b.id_kpicorp,
            b.id_sobject,
            a.index_kpibunit,
            b.year_kpicorp AS year_kpibunit,
            a.id_company AS compkpi_id
           FROM kpi_bisnis_unit_corps a
             LEFT JOIN kpi_corporate b ON b.id_kpicorp::text = a.id_kpicorp::text
        UNION ALL
         SELECT DISTINCT a.id_kpibunit,
            NULL::character varying AS id_kpicorp,
            a.id_sobject,
            a.index_kpibunit,
            a.year_kpibunit,
            a.id_company AS compkpi_id
           FROM kpi_bisnis_unit_support a) tbl;

CREATE OR REPLACE VIEW mini_kpi_department
 AS
 SELECT tbl.id_kpidept,
    tbl.id_kpibunit,
    tbl.id_sobject,
    tbl.index_kpidept,
    tbl.year_kpidept,
    tbl.compkpi_id,
    tbl.deptkpi_id
   FROM ( SELECT DISTINCT a.id_kpidept,
            b.id_kpibunit,
            b.id_sobject,
            a.index_kpidept,
            b.year_kpibunit AS year_kpidept,
            b.compkpi_id,
            a.id_department AS deptkpi_id
           FROM kpi_department_corps a
             JOIN kpi_bisnis_unit b ON b.id_kpibunit::text = a.id_kpibunit::text
        UNION ALL
         SELECT DISTINCT a.id_kpidept,
            NULL::character varying AS id_kpibunit,
            a.id_sobject,
            a.index_kpidept,
            a.year_kpidept,
            a.id_company AS compkpi_id,
            a.id_department AS deptkpi_id
           FROM kpi_department_support a) tbl;

CREATE OR REPLACE VIEW mini_kpi_divcorp
 AS
 SELECT a.id_kpidivcorp,
    b.id_kpicorp,
    b.id_sobject,
    a.index_kpidivcorp,
    b.year_kpicorp AS year_kpidivcorp,
    a.id_department AS deptkpi_id
   FROM kpi_divcorp_corps a
     LEFT JOIN kpi_corporate b ON b.id_kpicorp::text = a.id_kpicorp::text
UNION ALL
 SELECT a.id_kpidivcorp,
    NULL::character varying AS id_kpicorp,
    a.id_sobject,
    a.index_kpidivcorp,
    a.year_kpidivcorp,
    a.id_department AS deptkpi_id
   FROM kpi_divcorp_support a;

CREATE OR REPLACE VIEW target_distinct_kpibunit
 AS
 SELECT DISTINCT a.id_kpibunit,
    c.id_kpidept,
    d.id_kpidept_target,
    e.id_kpidept_real,
    a.target_kpibunit,
    d.month_kpidept,
    d.target_kpidept,
    e.value_kpidept_real,
    e.file_kpidept_real,
    a.compkpi_id,
    a.compkpi_name,
    c.deptkpi_id,
    c.deptkpi_name
   FROM kpi_bisnis_unit a
     JOIN kpi_department c ON c.id_kpibunit::text = a.id_kpibunit::text
     LEFT JOIN kpi_department_target d ON d.id_kpidept::text = c.id_kpidept::text
     LEFT JOIN kpi_department_realization e ON e.id_kpidept_target::text = d.id_kpidept_target::text;

CREATE OR REPLACE VIEW target_distinct_kpicorps_dept
 AS
 SELECT DISTINCT a.id_kpicorp,
    b.id_kpibunit,
    c.id_kpidept,
    d.id_kpidept_target,
    e.id_kpidept_real,
    a.target_kpicorp,
        CASE
            WHEN b.cascade_kpibunit::text = 'half-round'::text THEN b.target_kpibunit
            ELSE a.target_kpicorp
        END AS target_kpibunit,
    d.month_kpidept,
    d.target_kpidept,
    e.value_kpidept_real,
    e.file_kpidept_real,
    b.id_company,
    f.name_company,
    c.id_department,
    g.name_department
   FROM kpi_corporate a
     JOIN kpi_bisnis_unit_corps b ON b.id_kpicorp::text = a.id_kpicorp::text
     JOIN kpi_department_corps c ON c.id_kpibunit::text = b.id_kpibunit::text
     LEFT JOIN kpi_department_target d ON d.id_kpidept::text = c.id_kpidept::text
     LEFT JOIN kpi_department_realization e ON e.id_kpidept_target::text = d.id_kpidept_target::text
     LEFT JOIN company_master f ON f.id_company::text = b.id_company::text
     LEFT JOIN department_master g ON g.id_department::text = c.id_department::text;

CREATE OR REPLACE VIEW target_distinct_kpicorps_divcorp
 AS
 SELECT DISTINCT a.id_kpicorp,
    c.id_kpidivcorp,
    d.id_kpidivcorp_target,
    e.id_kpidivcorp_real,
    a.target_kpicorp,
    d.month_kpidivcorp,
    d.target_kpidivcorp,
    e.value_kpidivcorp_real,
    e.file_kpidivcorp_real,
    c.id_department,
    g.name_department
   FROM kpi_corporate a
     JOIN kpi_divcorp_corps c ON c.id_kpicorp::text = a.id_kpicorp::text
     LEFT JOIN kpi_divcorp_target d ON d.id_kpidivcorp::text = c.id_kpidivcorp::text
     LEFT JOIN kpi_divcorp_realization e ON e.id_kpidivcorp_target::text = d.id_kpidivcorp_target::text
     LEFT JOIN department_master g ON g.id_department::text = c.id_department::text;

CREATE OR REPLACE VIEW target_distinct_kpidept
 AS
 SELECT DISTINCT c.id_kpidept,
    d.id_kpidept_target,
    e.id_kpidept_real,
    d.month_kpidept,
    d.target_kpidept,
    e.value_kpidept_real,
    e.file_kpidept_real,
    c.deptkpi_id,
    c.deptkpi_name
   FROM kpi_department c
     LEFT JOIN kpi_department_target d ON d.id_kpidept::text = c.id_kpidept::text
     LEFT JOIN kpi_department_realization e ON e.id_kpidept_target::text = d.id_kpidept_target::text;

CREATE OR REPLACE VIEW target_distinct_kpidivcorp
 AS
 SELECT DISTINCT a.id_kpicorp,
    c.id_kpidivcorp,
    d.id_kpidivcorp_target,
    e.id_kpidivcorp_real,
    a.target_kpicorp,
    d.month_kpidivcorp,
    d.target_kpidivcorp,
    e.value_kpidivcorp_real,
    e.file_kpidivcorp_real,
    c.deptkpi_id,
    c.deptkpi_name
   FROM kpi_corporate a
     JOIN kpi_divcorp c ON c.id_kpicorp::text = a.id_kpicorp::text
     LEFT JOIN kpi_divcorp_target d ON d.id_kpidivcorp::text = c.id_kpidivcorp::text
     LEFT JOIN kpi_divcorp_realization e ON e.id_kpidivcorp_target::text = d.id_kpidivcorp_target::text;