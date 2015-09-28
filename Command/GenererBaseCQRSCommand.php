<?php

namespace Starkerxp\CQRSESBundle\Command;

use Starkerxp\CQRSESBundle\Generator\CQRSESGenerator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenererBaseCQRSCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this->setName('starkerxp:cqrs:generer:structure')
                ->addArgument('bundle', InputArgument::REQUIRED, "Le bundle dans lequel générer la structure")
                ->addArgument('domain', InputArgument::REQUIRED, "Le domain à générer")
                ->setDescription('Genere le code de base pour commencer a utiliser la syntaxe CQRS')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $nomBundle = $input->getArgument('bundle');
        $nomDomain = ucfirst($input->getArgument('domain'));

        // On recupere les infos sur le bundle nécessaire à la génération du controller
        $kernel = $this->getContainer()->get('kernel');
        $bundle = $kernel->getBundle($nomBundle);
        $namespace = $bundle->getNamespace();
        $path = $bundle->getPath();

        $bundleCQRSES = $kernel->getBundle("StarkerxpCQRSESBundle");
        $generator = new CQRSESGenerator();
        $generator->setSkeletonDirs($bundleCQRSES->getPath() . '/Resources/views/');
        $generator->generate($namespace, $path . '/Services/', $nomDomain);
    }

    public function genererFichiers($nomDomain, $path, $namespace, $namespaceFQC)
    {

        // On récupère le moteur de template twig
        $twig = $this->getContainer()->get('templating');

        foreach ($fichiersStructures as $repertoire => $fichiers) {
            $repertoireRenomme = str_replace("_nomDomaine_", $nomDomain, $repertoire);
            foreach ($fichiers as $fichier) {
                $fichierRenomme = str_replace(["_nomDomaine_", "_lnomDomaine_"], [$nomDomain, lcfirst($nomDomain)], $fichier);
                $repertoireDestination = $path . "/" . $repertoireRenomme . $fichierRenomme;
                $renderCode = $twig->render("StarkerxpCQRSESBundle:$repertoire:$fichier.twig", [
                    'nomDeDomaine' => $nomDomain,
                    'nomDeDomaineMinuscule' => lcfirst($nomDomain),
                    'namespace' => $namespace,
                    'namespaceFQC' => $namespaceFQC,
                ]);
                file_put_contents($repertoireDestination, $renderCode);
            }
        }
    }

}
