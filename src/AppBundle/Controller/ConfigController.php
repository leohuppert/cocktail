<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class ConfigController extends Controller
{
    /**
     * Charge la BDD
     *
     * @Route("/install")
     *
     * @param KernelInterface $kernel
     * @return Response
     * @throws \Exception
     */
    public function installAction(KernelInterface $kernel)
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);

        // Création BDD avec config dans parameters.yml

        $input = new ArrayInput(array(
            'command' => 'doctrine:database:create'
        ));

        $output = new BufferedOutput();
        $application->run($input, $output);

        $content = $output->fetch();

        // Créations des tables

        $input = new ArrayInput(array(
            'command' => 'doctrine:schema:update',
            '--force' => true
        ));

        $output = new BufferedOutput();
        $application->run($input, $output);

        $content = $content . $output->fetch();

        // Chargement des données

        $input = new ArrayInput(array(
            'command'          => 'doctrine:fixtures:load',
            '--no-interaction' => true
        ));

        $output = new BufferedOutput();
        $application->run($input, $output);

        $content = $content . $output->fetch();

        return new Response($content);
    }
}
