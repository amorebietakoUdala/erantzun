<?php

namespace App\Controller;

use App\Entity\Eskatzailea;
use App\Entity\Egoera;
use App\Form\EskatzaileaFormType as FormEskatzaileaFormType;
use App\Repository\EgoeraRepository;
use App\Repository\ErantzunaRepository;
use App\Repository\EskakizunaRepository;
use App\Repository\EskatzaileaRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ERANTZUN')]
#[Route(path: '/api')]
class ApiController extends AbstractController
{

    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly EntityManagerInterface $em,
        private readonly EskatzaileaRepository $eskatzaileaRepo,
        private readonly ErantzunaRepository $erantzunaRepo,
        private readonly EskakizunaRepository $eskakizunaRepo,
        private readonly EgoeraRepository $egoeraRepository,
    )
    {
    }

    #[Route(path: '/eskatzailea', name: 'api_eskatzailea_list', options: ['expose' => true], methods: ['GET'])]
    public function list(Request $request)
    {
        $criteria = $request->query->all();
        if ($criteria) {
            $eskatzaileak = $this->eskatzaileaRepo->findAllLike($criteria);
        } else {
            $eskatzaileak = $this->eskatzaileaRepo->findAll();
        }
        $response = $this->createApiResponse(['eskatzaileak' => $eskatzaileak], 200);

        return $response;
    }

    #[Route(path: '/eskatzailea', methods: ['POST'])]
    public function new(Request $request)
    {
        $eskatzailea = new Eskatzailea();
        $form = $this->createForm(FormEskatzaileaFormType::class, $eskatzailea);
        $this->processForm($request, $form);

        $this->em->persist($eskatzailea);
        $this->em->flush();

        $response = $this->createApiResponse($eskatzailea, 201);
        $eskatzaileaUrl = $this->generateUrl('api_eskatzailea_show', [
            'id' => $eskatzailea->getId()
        ]);
        $response->headers->set('Location', $eskatzaileaUrl);

        return $response;
    }

    #[Route(path: '/eskatzailea/{id}', name: 'api_eskatzailea_show', methods: ['GET'])]
    public function show($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ERANTZUN');
        $eskatzailea = $this->eskatzaileaRepo->findOneBy([
            'id' => $id,
        ]);

        if (!$eskatzailea) {
            throw $this->createNotFoundException('Ez da eskatzailerik aurkitu.');
        }

        $response = $this->createApiResponse($eskatzailea, 200);

        return $response;
    }

    #[Route(path: '/erantzuna/{id}', name: 'api_erantzuna_show', methods: ['GET'], options: ['expose' => true])]
    public function showErantzuna($id)
    {
        $erantzuna = $this->erantzunaRepo->findOneBy([
            'id' => $id,
        ]);
        if (!$erantzuna) {
            throw $this->createNotFoundException('Ez da erantzunik aurkitu.');
        }
        $response = $this->createApiResponse($erantzuna, 200);

        return $response;
    }

    #[Route(path: '/batchclose', name: 'api_eskakizuna_batchclose', methods: ['POST'])]
    public function close(Request $request)
    {
        $ids = \json_decode((string) $request->get('ids'));

        foreach ($ids as $id) {
            $eskakizuna = $this->eskakizunaRepo->find($id);
            $eskakizuna->setItxieraData(new \DateTime());
            $egoera = $this->egoeraRepository->find(Egoera::EGOERA_ITXIA);
            $eskakizuna->setEgoera($egoera);
            $this->em->persist($eskakizuna);
        }
        $this->em->flush();

        return $this->createApiResponse(
            ['Deskripzioa' => 'Eskakizun guztiak ondo itxi dira'], 200);
    }

    private function processForm(Request $request, FormInterface $form)
    {
        $data = json_decode($request->getContent(), true);
        $clearMissing = 'PATCH' != $request->getMethod();
        $form->submit($data, $clearMissing);
    }

    protected function createApiResponse($data, $statusCode = 200)
    {
        $json = $this->serialize($data);

        return new Response($json, $statusCode, ['Content-Type' => 'application/json']);
    }
    
    protected function serialize($data, $format = 'json')
    {
        $context = new SerializationContext();
        $context->setSerializeNull(true);
        $context->enableMaxDepthChecks();

        return $this->serializer->serialize($data, $format, $context);
    }    
}
