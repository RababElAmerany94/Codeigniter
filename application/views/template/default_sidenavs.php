<?php
$CI =& get_instance();
$CI->load->helper('general_helper');
$userdata = $CI->session->userdata();
foreach ($this->db->get('settings')->result() as $setting)
    $settings[$setting->key] = $setting->value;
$name = '';
$img = '';

if (isset($userdata) && isset($userdata['first_name']) && isset($userdata['last_name']) && isset($userdata['email'])) {
    $name = $userdata['first_name'] . ' ' . $userdata['last_name'];
    $img = 'https://www.gravatar.com/avatar/' . md5($userdata['email']);
}

?>
<div class="col-md-3 left_col menu_fixed">
    <div class="left_col scroll-view">
        <div class="navbar nav_title" style="border: 0;">
            <a href="<?php echo base_url(); ?>" class="site_title">
                <center><span style="font-size: 20px;"><?= $settings['app_nom'] ?></span></center>
            </a>
        </div>

        <div class="clearfix"></div>
        <!-- menu profile quick info -->
        <div class="profile clearfix">
            <div class="profile_pic">
                <img src="<?= $img ?>" alt="..." class="img-circle profile_img">
            </div>
            <div class="profile_info">
                <span>Bienvenue,</span>
                <h2><?= $name ?></h2>
            </div>
        </div>
        <br>
        <!-- Sidebar Menu -->
        <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
            <div class="menu_section">
                <h3>Menu</h3>
                <ul class="nav side-menu">
                    <li>
                        <a href="<?php echo base_url('/') ?>"><i class="fa fa-dashboard"></i> Tableau de Bord</a>
                    </li>

                    <li>
                        <a><i class="fa fa-dollar"></i> Ventes <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <?php if (can_list("Factures_clients")): ?>
                            <li><a href="<?php echo base_url('factures_clients/') ?>">Factures</a></li>
                            <?php endif; ?>
                            <?php if (can_list("Effets_clients")): ?>
                            <li><a href="<?php echo base_url('effets_clients/') ?>">Effets</a></li>
                            <?php endif; ?>
                            <?php if (can_list("Cheques")): ?>
                            <li><a href="<?php echo base_url('cheques/') ?>">Cheques</a></li>
                            <?php endif; ?>
                            <?php if (can_list("Virements_versements")): ?>
                            <li><a href="<?php echo base_url('virements_versements/') ?>">Virements et Versements</a></li>
                            <?php endif; ?>
                            <?php if (can_list("Avoirs")): ?>
                            <li><a href="<?php echo base_url('avoirs/') ?>">Avoirs</a></li>
                            <?php endif; ?>
                            <?php if (can_list("Clients")): ?>
                            <li><a href="<?php echo base_url('clients') ?>">Clients</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>

                    <li>
                        <a><i class="fa fa-truck"></i> Achats <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <?php if (can_list("Achats_import")): ?>
                            <li><a href="<?php echo base_url('achats_import/') ?>">Déclarations DUM</a></li>
                            <?php endif; ?>
                            <?php if (can_list("Credocs_fournisseurs")): ?>
                            <li><a href="<?php echo base_url('credocs_fournisseurs/') ?>">Credocs</a></li>
                            <?php endif; ?>
                            <?php if (can_list("Achats_locaux")): ?>
                            <li><a href="<?php echo base_url('achats_locaux/') ?>">Locaux</a></li>
                            <?php endif; ?>
                            <?php if (can_list("Effets_fournisseurs")): ?>
                            <li><a href="<?php echo base_url('effets_fournisseurs/') ?>">Effets</a></li>
                            <?php endif; ?>
                            <?php if (can_list("Engagements_importation")): ?>
                            <li><a href="<?php echo base_url('engagements_importation/') ?>">Engagements d'Importation</a></li>
                            <?php endif; ?>
                            <?php if (can_list("Fournisseurs")): ?>
                            <li><a href="<?php echo base_url('fournisseurs/') ?>">Fournisseurs</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <?php if (can_list("Controle_reglement_factures") || !can_list("Reglement_mixte") ||
                              can_list("Verification_factures")): ?>        
                    <li>
                        <a><i class="fa fa-dot-circle-o"></i> Règlement <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <?php if (can_list("Controle_reglement_factures")): ?>
                            <li><a href="<?php echo base_url('controle_reglement_factures') ?>">Contrôle de Règlement des Factures</a></li>
                            <?php endif; ?>
                            <?php if (can_list("Reglement_mixte")): ?>
                            <li><a href="<?php echo base_url('Reglement_mixte') ?>">Règlement Mixte</a></li>
                            <?php endif; ?>
                            <?php if (can_list("Verification_factures")): ?>
                            <li><a href="<?php echo base_url('Verification_factures') ?>">Vérification de Factures</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <?php endif; ?>
                    <?php if (can_list("Etats")): ?>        
                    <li>
                        <a><i class="fa fa-file"></i> Etats <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href="<?php echo base_url('etats_achats') ?>">Etats des Achats</a></li>
                            <li><a href="<?php echo base_url('etats_ca') ?>">Etats du C.A.</a></li>
                            <li><a href="<?php echo base_url('etats_carnets') ?>">Etats des Carnets</a></li>
                            <li><a href="<?php echo base_url('etats_cheques') ?>">Etats des Chèques</a></li>
                            <li><a href="<?php echo base_url('etats_clients') ?>">Etats des Clients</a></li>
                            <li><a href="<?php echo base_url('etats_effets') ?>">Etats des Effets</a></li>
                            <li><a href="<?php echo base_url('etats_effets_fournisseurs') ?>">Etats des Effets Fournisseurs</a></li>
                            <li><a href="<?php echo base_url('Etats_factures') ?>">Etats des Factures</a></li>
                            <li><a href="<?php echo base_url('etats_fournisseurs') ?>">Etats des Fournisseurs</a></li>
                            <li><a href="<?php echo base_url('etats_recapitulatifs') ?>">Etats Récapitulatifs</a></li>
                        </ul>
                    <li>
                    <?php endif; ?>
                    <li>
                        <a><i class="fa fa-cogs"></i> Système <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <?php if (can_list("Roles")): ?>
                            <li><a href="<?php echo base_url('roles/') ?>">Roles</a></li>
                            <?php endif; ?>
                            <?php if (can_list("Utilisateurs")): ?>
                            <li><a href="<?php echo base_url('utilisateurs/') ?>">Utilisateurs</a></li>
                            <?php endif; ?>
                            <?php if (can_list("Salaires")): ?>
                            <li><a href="<?php echo base_url('salaires/') ?>">Salaires</a></li>
                            <?php endif; ?>
                            <?php if (can_list("Banques")): ?>
                            <li><a href="<?php echo base_url('banques/') ?>">Banques</a></li>
                            <?php endif; ?>
                            <?php if (can_list("Taux_conversion")): ?>
                            <li><a href="<?php echo base_url('taux_conversion/') ?>">Taux de Conversion m/kg</a></li>
                            <?php endif; ?>
                            <?php if (can_list("Nature_marchandises")): ?>
                            <li><a href="<?php echo base_url('nature_marchandises/') ?>">Nature des Marchandises</a></li>
                            <?php endif; ?>
                            <?php if (can_list("Code_marchandises")): ?>
                            <li><a href="<?php echo base_url('code_marchandises/') ?>">Code des Marchandises</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        <!-- /Sidebar Menu -->
    </div>
</div>
