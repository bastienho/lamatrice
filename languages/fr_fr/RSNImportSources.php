<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

$languageStrings = array(
	'RSNImportSources' => 'Importations préconfigurées',
	'SINGLE_RSNImportSources' => 'Importation préconfigurée',
	'LBL_BLOCK_GENERAL_INFORMATION' => 'Informations générales',
	'LBL_BLOCK_SYSTEM_INFORMATION' => 'Informations du système',
	'LBL_TABID' => 'Table liée',
	'LBL_DISABLED' => 'Désactivée',
	'LBL_CLASS' => 'Classe',
	'LBL_SORTORDERID' => 'Index de tri',
	'LBL_DESCRIPTION' => 'Description',
	'LBL_MODIFICATIONS' => 'Modifications',

	'LBL_NEXT_BUTTON_LABEL' => 'Suivant',
	'LBL_IMPORT_BUTTON_LABEL' => 'Importer',
	'LBL_TRY_AGAIN' => 'Réessayer!',
	'LBL_IMPORT_STEP_1' => 'Etape 1', 
	'LBL_IMPORT_STEP_1_DESCRIPTION' => 'Choix de la source de données',
	'LBL_IMPORT_STEP_2' => 'Etape 2',
	'LBL_SELECT_FILE_STEP_DESCRIPTION' => 'Choix du fichier',
	'LBL_FROM' => 'Depuis',
	'LBL_CSV_FILE' => 'Fichier CSV',
	'LBL_DATABASE' => 'Base de données',
	'LBL_NO_DATA_SOURCE' => 'Aucune source de données disponible',
	'LBL_IMPORT_NEEDED_FILE_TYPE' => 'Le fichier doit être au format',
	'NOTHING_TO_CONFIGURE' => 'Rien à configurer',
	'LBL_IMPORT_NEEDED_FILE_ENCODING' => 'encodé en',
	'LBL_ADVANCED_FILE_CONFIGURATION' => 'Configuration avancée',
	'LBL_FILE_TYPE'                => 'Type de fichier'             , 
	'LBL_CHARACTER_ENCODING'       => 'Encodage des caractères'    , 
	'LBL_MAX_QUERY_ROWS'                => 'Nbre maximum de lignes'                  , 
	'LBL_DELIMITER'                => 'Délimiteur'                  , 
	'LBL_HAS_HEADER'               => 'Contient une ligne d\'entêtes',
	'LBL_IMPORT_NEEDED_FILE_DELIMITER' => 'délimité par des',
	'LBL_NEW_PRODUCT_FOUND_DURING_IMPORT' => 'produit inconnu ou en erreur trouvé pendant l\'import. Veuillez le corriger ou l\'ajouter avant d\'importer les factures',
	'LBL_NEW_PRODUCT_FOUND_DURING_IMPORT_PLURAL' => 'produits inconnus ou en erreur trouvés pendant l\'import. Veuillez les corriger ou ajouter avant d\'importer les factures',
	'LBL_NEW_CURRENCY_FOUND_DURING_IMPORT' => 'nouvelle devise trouvée pendant l\'import. Veuillez l\'ajouter avant d\'importer la suite',
	'LBL_NEW_CURRENCY_FOUND_DURING_IMPORT_PLURAL' => 'nouvelles devises trouvées pendant l\'import. Veuillez les ajouter avant d\'importer la suite',
	'LBL_RETURN' => 'Retour',
	'LBL_PRODUCTCODE' => 'code produit',
	'LBL_PRODUCTNAME' => 'nom du produit',
	'LBL_IMPORT_PREVIEW_FOR_MODULE' => 'Prévisualisation de l\'import pour le module',
	'LBL_ADD_NEW_PRODUCT' => 'Ajouter un nouveau produit',
	'LBL_ADD_NEW_SERVICE' => 'Ajouter un nouveau service',
	'LBL_IN_CASE_OF_PROBLEM_CALL' => 'En cas de problème, appelez',
	'LBL_RESULT'                   => 'Résultats'                   , 
	'LBL_TOTAL_RECORDS_IMPORTED'   => 'Nombre total d\'enregistrements importés', 
	'LBL_NUMBER_OF_RECORDS_CREATED' => 'Nombre d\'enregistrements créés', 
	'LBL_NUMBER_OF_RECORDS_UPDATED' => 'Nombre d\'enregistrements mis à jour', 
	'LBL_NUMBER_OF_RECORDS_SKIPPED' => 'Nombre d\'enregistrements ignorés', 
	'LBL_NUMBER_OF_RECORDS_MERGED' => 'Nombre d\'enregistrements fusionnés',
	'LBL_NUMBER_OF_RECORDS_DELETED' => 'Nombre d\'enregistrements supprimés', 
	'LBL_TOTAL_RECORDS_FAILED'     => 'Nombre d\'enregistrements échoués', 
	'LBL_IMPORT_MORE'              => 'Importer plus d\'éléments' , 
	'LBL_VIEW_LAST_IMPORTED_RECORDS' => 'Derniers enregistrements importés', 
	'LBL_UNDO_LAST_IMPORT'         => 'Annuler le dernier import'   , 
	'LBL_FINISH_BUTTON_LABEL'      => 'Terminer'                    ,
	'LBL_UNDO_RESULT'              => 'Annuler le résultat de l\'import', 
	'LBL_TOTAL_RECORDS'            => 'Nombre total d\'enregistrements', 
	'LBL_OK_BUTTON_LABEL'          => 'Ok',
	'LBL_IMPORT_SCHEDULED'         => 'Import programmé', 
	'LBL_RUNNING'                  => 'En cours', 
	'LBL_HALTED'                  => 'Suspendu',
	'LBL_VALIDATING'              => 'Validation des données à terminer',
	'LBL_REACTIVATE'                  => 'Réactiver', 
	'LBL_CANCEL_IMPORT'            => 'Annuler Import', 
	'LBL_ERROR'                    => 'Erreur',
	'LBL_CLEAR_DATA'               => 'Effacer les données', 
	'ERR_UNIMPORTED_RECORDS_EXIST' => 'Il existe des données non traités dans le processus d\'import, vous empéchant d\'importer des données pour ce module. <br>
					    Purger les données non importées pour relancer un nouvel import. <br>
					    Attention, peut-être qu\'une importation est en cours dans un autre module ou en cours de préparation, dans une phase intermédiaire : rafraichissez cette page d\'ici une minute. <br>
					    Si ce message persiste, c\'est qu\'il y a effectivement un problème.', 
	'ERR_IMPORT_INTERRUPTED'       => 'L\'import courant a été interrompu. Essayez plus tard.', 
	'ERR_FAILED_TO_LOCK_MODULE'    => 'Impossible de vérouiller ce module pour l\'import. Essayez plus tard', 
	'LBL_SELECT_SAVED_MAPPING'     => 'Select Saved Mapping', 
	'LBL_IMPORT_ERROR_LARGE_FILE'  => 'Fichier d\'import trop grand ',
	'LBL_FILE_UPLOAD_FAILED'       => 'Erreur pendant l\'import du fichier',
	'LBL_IMPORT_CHANGE_UPLOAD_SIZE' => 'Import Change Upload Size', // TODO: Review
	'LBL_IMPORT_DIRECTORY_NOT_WRITABLE' => 'Impossible d\'écrire dans le répertoire d\'import', 
	'LBL_IMPORT_FILE_COPY_FAILED'  => 'la copie du fichier d\'import a échoué',
	'LBL_INVALID_FILE'             => 'Fichier invalide', 
	'LBL_NO_ROWS_FOUND'            => 'Aucune colonne trouvée',
	'LBL_SCHEDULED_IMPORT_DETAILS' => 'Votre import a été programmé, les données seront importées progressivement.',
	'LBL_DETAILS'                  => 'Détails',
	'skipped'                      => 'Enregistrements ignorés', 
	'failed'                       => 'Enregistrements avec erreur', 
	'Skip'                       => 'Ignorer',
        
	'LBL_PRESTASHOP' => 'Prestashop',
	'LBL_PAYBOX' => 'PayBox',
	'LBL_PAYPAL' => 'PayPal',
	'LBL_COGILOG' => 'Cogilog',
	'LBL_DONATEURSWEB' => 'Donateurs web',
	'LBL_DONATEURSWEB_4D' => 'Donateurs web depuis 4D',
	
	'LBL_DBTYPE' => 'Type de base',
	'LBL_DBSERVER' => 'Serveur',
	'LBL_DBPORT' => 'Port',
	'LBL_DBNAME' => 'Nom de la base',
	'LBL_DBUSER' => 'Utilisateur',
	'LBL_DBPWD' => 'Mot de passe',
	
	'LBL_SELECT_Documents_STEP' => 'Sélection d\'un document lié',
	'LBL_SELECT_Documents_STEP_DESCRIPTION' => 'Veuillez sélectionner le document lié',
	
	'LBL_SELECT_Critere4D_STEP' => 'Sélection d\'un critère lié',
	'LBL_SELECT_Critere4D_STEP_DESCRIPTION' => 'Veuillez sélectionner le critère lié',
	
	
	'LBL_RECORDID_STATUS_0' => 'Inconnu',
	'LBL_RECORDID_STATUS_1' => 'Trouvé',
	'LBL_RECORDID_STATUS_2' => 'A créer',
	'LBL_RECORDID_STATUS_3' => 'A mettre à jour',
	'LBL_RECORDID_STATUS_4' => 'Annulé',
	'LBL_RECORDID_STATUS_5' => 'Plus tard',
	'LBL_RECORDID_STATUS_10' => 'Une proposition à vérifier',
	'LBL_RECORDID_STATUS_11' => 'Une proposition fiable',
	'LBL_RECORDID_STATUS_12' => 'Plusieurs propositions',
	
	'LBL_VALIDATE_SELECTED_CONTACT_ROWS' => 'Valider les contacts sélectionnés',
);

?>
