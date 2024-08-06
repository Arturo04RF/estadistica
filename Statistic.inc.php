<?php

import('lib.pkp.classes.plugins.GenericPlugin');

class StatisticPlugin extends GenericPlugin {

    // Método de registro del plugin
    function register($category, $path, $mainContextId = null) {
        // If the parent class registers successfully
        if (parent::register($category, $path, $mainContextId)) {
            // Register a hook to add the button to the template display
            HookRegistry::register('TemplateManager::display', array($this, 'callbackStatisticsButton'));
            return true;
        }
        return false;
    }

    // Devuelve el nombre para mostrar del plugin
    function getDisplayName() {
        return __('plugins.generic.statistics.name');
    }

    // Devuelve la descripción del plugin
    function getDescription() {
        return __('plugins.generic.statistics.description');
    }

    // Callback para inyectar el botón en la plantilla
    function callbackStatisticsButton($hookName, $args) {
        $templateMgr = $args[0];
        $template = $args[1];

        // Inyectar el botón en la plantilla deseada, por ejemplo, en el header
        if ($template == 'frontend/components/header.tpl') {
            $output = '<button id="statisticsButton" class="button">Ver Estadísticas</button>';
            $templateMgr->assign('statisticsButton', $output);
        }

        return false;
    }
}






