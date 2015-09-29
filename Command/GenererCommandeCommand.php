<?php

namespace Starkerxp\CQRSESBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenererCommandeCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this->setName('cqrses:generer:commande')
                ->addArgument('bundle', InputArgument::REQUIRED, "Le bundle dans lequel générer la structure")
                ->addArgument('domaine', InputArgument::REQUIRED, "Le domain à générer")
                ->addArgument('commande', InputArgument::REQUIRED, "La commande à générer")
                ->setDescription('Genere le code de base pour commencer a utiliser la syntaxe CQRS')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bundle = $input->getArgument('bundle');
        $nomDomaine = ucfirst($input->getArgument('domaine'));
        $commande = ucfirst($input->getArgument('commande'));
        $path = str_replace("\\", "/", realpath("./src/" . $bundle));
        $pathCommand = $path . "/Services/Command/" . $nomDomaine;

        // Permet de générer l'id de base.
        $nomDomaineString = lcfirst($nomDomaine);
        $idString = $nomDomaineString . "Id";

        if (!$this->leDossierEstIlUtilisable($pathCommand)) {
            return false;
        }
        $fichierBase = $pathCommand . "/" . $commande;
        $fichiersExtension = [
            "Command" => "template1",
            "Handler" => "template2",
        ];
        foreach ($fichiersExtension as $extension => $template) {
            $fichier = $fichierBase . $nomDomaine . $extension;
            $output->writeln($fichier . " " . $template);
        }
    }

    /**
     * Permet de vérifier si un dossier est exploitable.
     *
     * @param type $dir
     * @return boolean
     * @throws \RuntimeException
     */
    public function leDossierEstIlUtilisable($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }
        if (!is_dir($dir)) {
            throw new \RuntimeException(sprintf("Unable to generate the bundle as the target directory \"%s\" exists but is a file.", realpath($dir)));
        }
        $files = scandir($dir);
        if ($files != array('.', '..')) {
            throw new \RuntimeException(sprintf('Unable to generate the bundle as the target directory "%s" is not empty.', realpath($dir)));
        }
        if (!is_writable($dir)) {
            throw new \RuntimeException(sprintf('Unable to generate the bundle as the target directory "%s" is not writable.', realpath($dir)));
        }
    }

}
