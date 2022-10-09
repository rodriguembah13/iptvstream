<?php


namespace App\Controller\Api;


use App\Entity\Customer;
use App\Repository\BouquetRepository;
use App\Repository\CountryRepository;
use App\Repository\CustomerRepository;
use App\Service\EndpointService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IptvApiController extends AbstractFOSRestController
{

    private $logger;
    private $endpointsService;
    private $bouquetRepository;

    /**
     * IptvApiController constructor.
     */
    public function __construct(BouquetRepository $bouquetRepository,LoggerInterface $logger, EndpointService $endpointService)
    {
        $this->logger = $logger;
        $this->endpointsService = $endpointService;
        $this->bouquetRepository=$bouquetRepository;
    }

    /**
     * @Rest\Get("/v1/countries", name="api_getallcountries")
     */
    public function getallcountries()
    {
        $view = $this->view($this->countryRepository->findAll(), Response::HTTP_OK, []);
        return $this->handleView($view);
    }

    /**
     * @Rest\Get("/v1/livestreamcategories", name="api_livestreamcategories")
     * @return Response
     */
    public function getlivecategories()
    {
        $values = $this->endpointsService->getLiveStreamCategory();

        $view = $this->view($values, Response::HTTP_OK, []);
        return $this->handleView($view);
    }

    /**
     * @Rest\Get("/v1/livestream", name="api_livestream")
     * @return Response
     */
    public function getlivestreambycategory(Request $request)
    {
        $values = $this->endpointsService->getLiveStreambyCategory($request->get('category'));

        $view = $this->view($values, Response::HTTP_OK, []);
        return $this->handleView($view);
    }
    /**
     * @Rest\Get("/v1/seriescategories", name="api_seriescategories")
     * @return Response
     */
    public function getseriecategories()
    {
        $values = $this->endpointsService->getSeriesCategory();

        $view = $this->view($values, Response::HTTP_OK, []);
        return $this->handleView($view);
    }
    /**
     * @Rest\Get("/v1/moviescategories", name="api_moviescategories")
     * @return Response
     */
    public function getmoviecategories()
    {
        $values = $this->endpointsService->getVodStreamCategory();

        $view = $this->view($values, Response::HTTP_OK, []);
        return $this->handleView($view);
    }

    /**
     * @Rest\Get("/v1/moviebycategory", name="api_moviebycategory")
     * @param Request $request
     * @return Response
     */
    public function getmoviesbycategory(Request $request)
    {
        $values = $this->endpointsService->getVodStreambyCategory($request->get('category'));

        $view = $this->view($values, Response::HTTP_OK, []);
        return $this->handleView($view);
    }

    /**
     * @Rest\Get("/v1/seriecategory", name="seriecategory")
     * @param Request $request
     * @return Response
     */
    public function getseriebycategory(Request $request)
    {
        $values = $this->endpointsService->getSeriebyCategory($request->get('category'));

        $view = $this->view($values, Response::HTTP_OK, []);
        return $this->handleView($view);
    }

    /**
     * @Rest\Get("/v1/serieinfo", name="serieinfo")
     * @param Request $request
     * @return Response
     */
    public function getserieinfo(Request $request)
    {
        $values = $this->endpointsService->getSerieStreamInfoByID($request->get('serie'));

        $view = $this->view($values, Response::HTTP_OK, []);
        return $this->handleView($view);
    }

    /**
     * @Rest\Get("/v1/bouquets", name="api_bouquets")
     * @return Response
     */
    public function getBouquets()
    {
        $bouquets = $this->bouquetRepository->findAll();
        $arrays = [];
        foreach ($bouquets as $bouquet) {
            $arrays[] = [
                'id' => $bouquet->getId(),
                'bouquetid' => $bouquet->getBouquetid(),
                'name' => $bouquet->getName(),
                'price' => $bouquet->getPrice(),
                'channelsize'=>count($bouquet->getChanelids()),
                'seriesize'=>count($bouquet->getSerieids()),
                'datecreation' => $bouquet->getBouquetorder(),
            ];
        }
        $view = $this->view($arrays, Response::HTTP_OK, []);
        return $this->handleView($view);
    }
    /**
     * @Rest\Get("/v1/bouquetcustomer/{id}", name="api_bouquetcustomer")
     * @param Customer $customer
     * @return Response
     */
    public function getBouquetByCustomer(Customer $customer)
    {
        $bouquetids = $customer->getCompte()->getBouquets();
        $arrays = [];
        foreach ($this->bouquetRepository->findByBouquetIds($bouquetids) as $bouquet) {
            $arrays[] = [
                'id' => $bouquet->getId(),
                'bouquetid' => $bouquet->getBouquetid(),
                'name' => $bouquet->getName(),
                'price' => $bouquet->getPrice(),
                'channelsize'=>count($bouquet->getChanelids()),
                'seriesize'=>count($bouquet->getSerieids()),
                'datecreation' => $bouquet->getBouquetorder(),
            ];
        }
        $view = $this->view($arrays, Response::HTTP_OK, []);
        return $this->handleView($view);
    }
    /**
     * @Rest\Get("/v1/bouquetchanelcustomer/{id}", name="api_bouquetchanelcustomer")
     * @param Customer $customer
     * @return Response
     */
    public function getBouquetchanelByCustomer(Customer $customer,Request $request)
    {
        $bouquet = $this->bouquetRepository->find($request->get('bouquet'));

        $data=$this->endpointsService->getLiveStreams();
        $arrays_= array_filter($data,function ($item) use($bouquet){
            if (in_array($item['num'],$bouquet->getChanelids())){
                return true;
            }else{
                return false;
            }
        });
   /*     $data_series=$this->endpointsService->getSeries();
        $arrayseries_= array_filter($data_series,function ($item) use($bouquet){
            if (in_array($item['num'],$bouquet->getSerieids())){
                return true;
            }else{
                return false;
            }
        });*/
        $arrays = [];
        $arrays_series = [];
        foreach ($arrays_ as $channel) {
            $arrays[] = [
                'num' => $channel['num'],
                'name' => $channel['name'],
                'stream_type' => $channel['stream_type'],
                'stream_id' => $channel['stream_id'],
                'stream_icon' => $channel['stream_icon'],
                'epg_channel_id' => $channel['epg_channel_id'],
                'added' => $channel['added'],
                'category_id' => $channel['category_id'],
                'custom_sid' => $channel['custom_sid'],
                'tv_archive' => $channel['tv_archive'],
                'direct_source' => $channel['direct_source'],
                'tv_archive_duration' => $channel['tv_archive_duration'],
            ];
        }
       /* foreach ($arrayseries_ as $serie) {
            $arrays_series[] = [
                'num' => $serie['num'],
                'name' => $serie['name'],
                'stream_type' => $serie['stream_type'],
                'stream_id' => $serie['stream_id'],
                'stream_icon' => $serie['stream_icon'],
                'epg_channel_id' => $serie['epg_channel_id'],
                'added' => $serie['added'],
                'category_id' => $serie['category_id'],
                'custom_sid' => $serie['custom_sid'],
                'tv_archive' => $serie['tv_archive'],
                'direct_source' => $serie['direct_source'],
                'tv_archive_duration' => $serie['tv_archive_duration'],
            ];
        }*/
        $view = $this->view(
           $arrays
        , Response::HTTP_OK, []);
        return $this->handleView($view);
    }

    /**
     * @Rest\Get("/v1/livestream_url", name="api_livestream_url")
     * @return Response
     */
    public function getlivestream_url(Request $request)
    {
        $values = $this->endpointsService->getLiveStreambyCategory($request->get('category'));

        $view = $this->view($values, Response::HTTP_OK, []);
        return $this->handleView($view);
    }

    /**
     * @Rest\Get("/v1/isvalid/{id}", name="api_getisvalid")
     *
     */
    function isvalid_test(Customer $customer)
    {
        $day = new \DateTime('now');
        $date_future = date_create("2022-09-30");
        $date_past = date_create("2022-08-06");
        $nbre = date_diff(date_create("2022-09-29"), new \DateTime('now'))->days;
        if ($customer->getDatecreation() <= $day) {
            $this->logger->error("message------");
        }
        if ($date_future == $day) {
            $this->logger->error("message futiure------");
        }
        $mnth = 2;
        $view = $this->view(['val' => "",
            'res' => $day->getTimestamp(),
            'aut' => $nbre], Response::HTTP_OK, []);
        return $this->handleView($view);
    }

    function isvalid(Customer $customer, $date)
    {
        $day = new \DateTime('now');
        $date_ = date_create($date);
        if ($day < $date_) {
            // return  true;
        }
        if ($customer->getExpiredAt() <= $date_) {
            $return = false;
        } else {
            $return = true;
        }
        return $return;
    }
}
