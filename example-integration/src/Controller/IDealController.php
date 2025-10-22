<?php

namespace OmniKassa\ExampleIntegration\Controller;

use OmniKassa\ExampleIntegration\Service\Service\Contract\OmniKassaClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IDealController extends AbstractController
{
    private OmniKassaClientInterface $omniKassaClient;

    public function __construct(OmniKassaClientInterface $omniKassaClient)
    {
        $this->omniKassaClient = $omniKassaClient;
    }

    #[Route('/ideal-issuers', name: 'ideal_issuers')]
    public function idealIssuers(): Response
    {
        $issuers = null;
        $error = null;

        try {
            $issuers = $this->omniKassaClient->getAllIdealIssuers();
        } catch (\Exception $e) {
            $error = 'Unable to retrieve iDEAL issuers: '.$e->getMessage();
        }

        return $this->render('home/ideal_issuers.html.twig', [
            'issuers' => $issuers,
            'error' => $error,
        ]);
    }
}
