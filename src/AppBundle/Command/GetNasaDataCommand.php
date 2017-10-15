<?php

namespace AppBundle\Command;

use AppBundle\Entity\Asteroid;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Unirest;

class GetNasaDataCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:get-nasa-data')
            ->setDescription('Gets NASA data.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Requesting data from NASA...');

        $headers = array('Accept' => 'application/json');
        $startDate = date('Y-m-d', time() - 2 * 86400);
        $endDate = date('Y-m-d');
        $apiKey = 'N7LkblDsc5aen05FJqBQ8wU4qSdmsftwJagVK7UD';

        $query = array(
            'start_date' => $startDate,
            'end_date' => $endDate,
            'api_key' => $apiKey
        );

        $response = Unirest\Request::get('https://api.nasa.gov/neo/rest/v1/feed', $headers, $query);
        $em = $this->getContainer()->get('doctrine')->getManager();

        foreach ($response->body->near_earth_objects as $date => $info) {
            foreach ($info as $data) {
                $asteroid = $this->getContainer()->get('doctrine')
                    ->getRepository(Asteroid::class)
                    ->findOneByReferenceId($data->neo_reference_id);

                $asteroid = $asteroid ? $asteroid : new Asteroid();
                $asteroid->setDate(new \DateTime($date));
                $asteroid->setName($data->name);
                $asteroid->setReferenceId($data->neo_reference_id);
                $asteroid->setSpeed($data->close_approach_data[0]->relative_velocity->kilometers_per_hour);
                $asteroid->setIsHazardous($data->is_potentially_hazardous_asteroid);

                $em->persist($asteroid);
                $em->flush();
            }
        }

        $output->writeln('Done.');
    }
}