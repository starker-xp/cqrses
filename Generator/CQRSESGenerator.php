<?php

namespace Starkerxp\CQRSESBundle\Generator;

use Sensio\Bundle\GeneratorBundle\Generator\Generator;

class CQRSESGenerator extends Generator
{

    public function generate($namespace, $dir, $domain)
    {
        if (file_exists($dir)) {
            if (!is_dir($dir)) {
                throw new \RuntimeException(sprintf('Unable to generate the bundle as the target directory "%s" exists but is a file.', realpath($dir)));
            }
            $files = scandir($dir);
            if ($files != array('.', '..')) {
                throw new \RuntimeException(sprintf('Unable to generate the bundle as the target directory "%s" is not empty.', realpath($dir)));
            }
            if (!is_writable($dir)) {
                throw new \RuntimeException(sprintf('Unable to generate the bundle as the target directory "%s" is not writable.', realpath($dir)));
            }
        }

        $parameters = array(
            'nomDeDomaine' => $domain,
            'nomDeDomaineMinuscule' => lcfirst($domain),
            'namespace' => $namespace,
            'namespaceFQC' => str_replace("\\", "\\\\", $namespace),
        );
        $fichiers = $this->getFichiers();

        foreach ($fichiers as $fichier) {
            $twigTemplate = "Services/" . $fichier . ".twig";
            $twigTemplate = str_replace("Services/../", "", $twigTemplate);

            $target = $dir . str_replace(['_nomDomaine_', '_lnomDomaine_'], [$domain, lcfirst($domain)], $fichier);
            $target = str_replace("Services/../", "", $target);
            $this->renderFile($twigTemplate, $target, $parameters);
        }
    }

    public function getFichiers()
    {
        return [
            '../Resources/config/services._lnomDomaine_.yml',
            'Command/_nomDomaine_/Creer_nomDomaine_Command.php',
            'Command/_nomDomaine_/Creer_nomDomaine_Handler.php',
            'Command/_nomDomaine_/Modifier_nomDomaine_Command.php',
            'Command/_nomDomaine_/Modifier_nomDomaine_Handler.php',
            'Command/_nomDomaine_/Supprimer_nomDomaine_Command.php',
            'Command/_nomDomaine_/Supprimer_nomDomaine_Handler.php',
            'Domain/_nomDomaine_/_nomDomaine_Collection.php',
            'Domain/_nomDomaine_/_nomDomaine_DTO.php',
            'Domain/_nomDomaine_/_nomDomaine_POPO.php',
            'Domain/_nomDomaine_/_nomDomaine_Domain.php',
            'Domain/_nomDomaine_/Event/_nomDomaine_AEteCree.php',
            'Domain/_nomDomaine_/Event/_nomDomaine_AEteModifie.php',
            'Domain/_nomDomaine_/Event/_nomDomaine_AEteSupprime.php',
            'Persistence/Ecriture/_nomDomaine_/_nomDomaine_Projection.php',
            'Persistence/Ecriture/_nomDomaine_/_nomDomaine_Repository.php',
            'Persistence/Lecture/_nomDomaine_Repository.php',
            'Query/_nomDomaine_/_nomDomaine_ListerQuery.php',
            'Query/_nomDomaine_/_nomDomaine_ListerQueryHandler.php',
            'Query/_nomDomaine_/_nomDomaine_Query.php',
            'Query/_nomDomaine_/_nomDomaine_QueryHandler.php',
        ];
    }

}
