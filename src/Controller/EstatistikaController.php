<?php

namespace App\Controller;

use App\Entity\Estatistika;
use App\Form\EstatistikaFormType;
use App\Repository\EstatistikaRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use SaadTazi\GChartBundle\DataTable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/{_locale}/estatistika')]
class EstatistikaController extends AbstractController
{
    public function __construct(
        private readonly EstatistikaRepository $estatistikaRepo,
        private readonly ?TranslatorInterface $translator = null,
    )
    {
    }

    #[Route(path: '/enpresako', name: 'admin_estatistika_enpresako')]
    public function estatistikaEnpresako(Request $request)
    {
        $estatistikaForm = $this->createForm(EstatistikaFormType::class);
        $criteria = [
            'noiztik' => DateTime::createFromFormat('Y-m-d H:i:s', date('Y').'-01-01 00:00:00'),
        ];
        $estatistikak = $this->estatistikaRepo->findEskakizunKopuruakNoiztikNora($criteria);
        $estatistikaForm->handleRequest($request);

        if ($estatistikaForm->isSubmitted() && $estatistikaForm->isValid()) {
            $criteria = $estatistikaForm->getData();
            $estatistikak = $this->estatistikaRepo->findEskakizunKopuruakNoiztikNora($criteria);
        }
        $estatiskaBatuak = $this->sumValuesOfTheSameKey($estatistikak);
        $dataTable2 = $this->createDataTable($estatistikak);
        $guztira = array_reduce($estatistikak, fn($i, $obj) => $i += $obj->getEskakizunak());
        return $this->render('/estatistika/estatistika.html.twig', [
            'dataTable2' => $dataTable2->toArray(),
            'estatistikak' => $estatiskaBatuak,
            'estatistikaForm' => $estatistikaForm->createView(),
            'guztira' => $guztira,
        ]);
    }

    private function createDataTable($results)
    {
        $data = $this->createDataForDataTable($results);
        $distinctUrteak = $this->urteEzberdinak($results);
        $dataTable2 = new DataTable\DataTable();
        $dataTable2->addColumn('Enpresa', 'Enpresa', 'string');
        foreach ($distinctUrteak as $urtea) {
            $dataTable2->addColumn($urtea, $urtea, 'number');
        }
        foreach ($data as $key => $value) {
            $urtekoEskakizunak = $value;
            $cells = [new DataTable\DataCell($key)];
            foreach ($urtekoEskakizunak as $key2 => $value2) {
                $cells[] = new DataTable\DataCell($value2, $this->translator->trans('messages.eskakizun', ['count' => intval($value2)]));
            }
            $dataTable2->addRowObject(new DataTable\DataRow($cells));
        }

        return $dataTable2;
    }

    private function sumValuesOfTheSameKey($myArray)
    {
        $sumArray = [];
        $currentEnpresa = '';
        foreach ($myArray as $estatistika) {
            $currentEnpresa = $estatistika->getEnpresa();
            if (array_key_exists($currentEnpresa, $sumArray)) {
                $sumArray[$currentEnpresa] += $estatistika->getEskakizunak();
            } else {
                $sumArray[$currentEnpresa] = $estatistika->getEskakizunak();
            }
        }

        return $sumArray;
    }

    private function urteEzberdinak($results)
    {
        $urteak = [];
        foreach ($results as $result) {
            $urteak[] = $result->getUrtea();
        }
        $distinctUrteak = array_unique($urteak);

        return $distinctUrteak;
    }

    private function createDataForDataTable($results)
    {
        $data = [];
        foreach ($results as $result) {
            if (!array_key_exists($result->getEnpresa(), $data)) {
                $data[$result->getEnpresa()][$result->getUrtea()] = $result->getEskakizunak();
            } else {
                $data[$result->getEnpresa()][$result->getUrtea()] = $result->getEskakizunak();
            }
        }

        return $data;
    }
}
