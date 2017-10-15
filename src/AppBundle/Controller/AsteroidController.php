<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Asteroid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use DoctrineExtensions\Query\Mysql\Year;

class AsteroidController extends Controller
{
    /**
     * @Route("/neo/hazardous")
     * @Method("GET")
     */
    public function getHazardousAsteroidsAction()
    {
        $asteroids = $this->getDoctrine()
            ->getRepository(Asteroid::class)
            ->findBy(array(
                'isHazardous' => true
            ));

        $json = $this->serialize($asteroids);
        $response = new Response($json, 200);

        return $response;
    }

    /**
     * @Route("/neo/fastest/{isHazardous}",
     *  requirements={
     *    "isHazardous": "true|false",
     *  }
     * )
     * @Method("GET")
     */
    public function getFastestAsteroidAction($isHazardous = "false")
    {
        $isHazardous = ($isHazardous === "true");

        $asteroid = $this->getDoctrine()
            ->getRepository(Asteroid::class)
            ->findOneBy(
                array(
                    'isHazardous' => $isHazardous,
                ),
                array('speed' => 'DESC')
            );

        $json = $this->serialize($asteroid);
        $response = new Response($json, 200);

        return $response;
    }

    /**
     * @Route("/neo/best-year/{isHazardous}",
     *  requirements={
     *    "isHazardous": "true|false",
     *  }
     * )
     * @Method("GET")
     */
    public function getBestYearAction($isHazardous = "false")
    {
        $repository = $this->getDoctrine()
            ->getRepository(Asteroid::class);

        $query = $repository->createQueryBuilder('a')
            ->select('YEAR(a.date) as year, COUNT(a.id) as cnt')
            ->where('a.isHazardous = :isHazardous')
            ->setParameter('isHazardous', ($isHazardous === "true") ? '1' : '0')
            ->groupBy('year')
            ->orderBy('cnt', 'DESC')
            ->getQuery();

        $result = $query->getResult();

        $data = array(
          'year' => $result[0]['year']
        );

        return new Response(json_encode($data));
    }

    /**
     * @Route("/neo/best-month/{isHazardous}",
     *  requirements={
     *    "isHazardous": "true|false",
     *  }
     * )
     * @Method("GET")
     */
    public function getBestMonthAction($isHazardous = "false")
    {
        $repository = $this->getDoctrine()
            ->getRepository(Asteroid::class);

        $query = $repository->createQueryBuilder('a')
            ->select('YEAR(a.date) as year, MONTH(a.date) as month, COUNT(a.id) as cnt')
            ->where('a.isHazardous = :isHazardous')
            ->setParameter('isHazardous', ($isHazardous === "true") ? '1' : '0')
            ->groupBy('year, month')
            ->orderBy('cnt', 'DESC')
            ->getQuery();

        $result = $query->getResult();

        $data = array(
            'year' => $result[0]['year'],
            'month' => $result[0]['month'],
        );

        return new Response(json_encode($data));
    }

    private function serialize($data)
    {
        return $this->container->get('jms_serializer')
            ->serialize($data, 'json');
    }
}