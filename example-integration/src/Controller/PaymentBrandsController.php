<?php

namespace OmniKassa\ExampleIntegration\Controller;

use OmniKassa\ExampleIntegration\Service\Service\Contract\OmniKassaClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentBrandsController extends AbstractController
{
    private OmniKassaClientInterface $omniKassaClient;

    public function __construct(OmniKassaClientInterface $omniKassaClient)
    {
        $this->omniKassaClient = $omniKassaClient;
    }

    #[Route('/payment-brands', name: 'payment_brands')]
    public function paymentBrands(): Response
    {
        $brands = null;
        $error = null;

        try {
            $brands = $this->omniKassaClient->getAllPaymentBrands();
        } catch (\Exception $e) {
            $error = 'Unable to retrieve payment brands: '.$e->getMessage();
        }

        return $this->render('home/payment_brands.html.twig', [
            'brands' => $brands,
            'error' => $error,
        ]);
    }
}
