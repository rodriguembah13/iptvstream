<?php

namespace App\Command;

use App\Entity\Bouquet;
use App\Entity\Customer;
use App\Entity\User;
use App\Repository\BouquetRepository;
use App\Repository\UserRepository;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ImportDataCommand extends Command
{
    private $users = [];
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;
    /**
     * Connection to the Kimai v2 database to write imported data to
     * @var ManagerRegistry
     */
    private $doctrine;
    /**
     * Validates the entities before they will be created
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * Connection to the old database to import data from
     * @var Connection
     */
    private $connection;
    /**
     * Prefix for the v1 database tables.
     * @var string
     */
    private $dbPrefix = '';
    protected static $defaultName = 'app:import-data';
    protected static $defaultDescription = 'Add a short description for your command';
    private $bouquetRepository;
    private $userRepository;
    private $logger;

    /**
     * ImportDataCommand constructor.
     * @param BouquetRepository $bouquetRepository
     * @param UserRepository $userRepository
     * @param UserPasswordHasherInterface $encoder
     * @param ManagerRegistry $registry
     * @param ValidatorInterface $validator
     */
    public function __construct(LoggerInterface $logger,BouquetRepository $bouquetRepository,UserRepository $userRepository,UserPasswordHasherInterface $encoder, ManagerRegistry $registry,
                                ValidatorInterface $validator)
    {
        $this->encoder = $encoder;
        $this->doctrine = $registry;
        $this->validator = $validator;
        $this->userRepository=$userRepository;
        $this->bouquetRepository=$bouquetRepository;
        $this->logger=$logger;

        parent::__construct();
    }


    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');
        $config = new Configuration();


        try {
            $connectionParams = ['url' => "mysql://user_iptvpro:vIHRvCDxKoerLYhFGdC@10.1.1.252:7999/xtream_iptvpro?charset=utf8&autoReconnect=true"];
            $this->connection = DriverManager::getConnection($connectionParams, $config);
            $this->dbPrefix = "";
            $password = 'tvplus';
            $users = $this->fetchAllFromImport('users');
            $bouquets = $this->fetchAllFromImport('bouquets');
        } catch (Exception $ex) {
            $io->error('Failed to load users: ' . $ex->getMessage());
            return 1;
        }
        try {
            $counter = $this->importUsers($io,$users);
            $counter2 = $this->importBouquet($io,$bouquets);
           /* $io->success('Imported users: ' . $counter);*/
            $io->success('Imported bouquets: ' . count($users));
        } catch (Exception $ex) {
            $io->error('Failed to import users: ' . $ex->getMessage() . PHP_EOL . $ex->getTraceAsString());
            return 1;
        }

        return Command::SUCCESS;
    }
    /**
     * @param string $table
     * @param array $where
     * @return array
     */
    protected function fetchAllFromImport($table, array $where = [])
    {
        $query = $this->connection->createQueryBuilder()
            ->select('*')
            ->from($this->connection->quoteIdentifier($this->dbPrefix . $table));

        foreach ($where as $column => $value) {
            $query->andWhere($query->expr()->eq($column, $value));
        }

        return $query->execute()->fetchAll();
    }

    /**
     * @return ManagerRegistry
     */
    protected function getDoctrine()
    {
        return $this->doctrine;
    }

    /**
     * @param SymfonyStyle $io
     * @param object $object
     * @return bool
     */
    protected function validateImport(SymfonyStyle $io, $object)
    {
        $errors = $this->validator->validate($object);

        if ($errors->count() > 0) {
            /** @var ConstraintViolation $error */
            foreach ($errors as $error) {
                $io->error(
                    (string) $error
                );
            }

            return false;
        }

        return true;
    }
    protected function importUsers(SymfonyStyle $io, $users)
    {
        $config2 = new Configuration();
        $connectionParams = ['url' => "mysql://symfony:Symfony123*@127.0.0.1:3306/iptvplus?charset=utf8"];
        $connection2 = DriverManager::getConnection($connectionParams, $config2);
        $entityManager = $this->getDoctrine()->getManager();
        foreach ($users as $oldUser) {
            $user=$this->userRepository->findOneBy(['userid'=>$oldUser['id']]);
            if (is_null($user)){
                $user=new User();
                $user->setUserid($oldUser['id']);
                $plainPassword = "iptv";
                $hashedPassword = $this->encoder->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
                $user->setRoles(["ROLE_USER"]);
                $user->setIsactivate(true);
                $this->doctrine->getManager()->persist($user);
                $customer=new Customer();
                $customer->setCompte($user);
                $date1 = date_create(date("Y-m-d "),new \DateTimeZone('Africa/Brazzaville'));
                $customer->setDatecreation($date1);
                $entityManager->persist($customer);
            }
            $user->setName($oldUser['username']);
            $user->setEmail($oldUser['username']."@iptv.com");
           // $str_arr = explode (",", $oldUser['bouquet']);
            $user->setUsername($oldUser['username']);
            $user->setBouquets(json_decode($oldUser['bouquet'], JSON_FORCE_OBJECT));
            $entityManager->flush();
        }
    }
    protected function importBouquet(SymfonyStyle $io, $bouquets)
    {
        foreach ($bouquets as $oldbouquet) {
            $bouquet=$this->bouquetRepository->findOneBy(["bouquetid"=>$oldbouquet['id']]);
            if (is_null($bouquet)){
                $bouquet=new Bouquet();
                $bouquet->setBouquetid($oldbouquet['id']);
                $bouquet->setPrice(0.0);
                $this->doctrine->getManager()->persist($bouquet);
            }
            $bouquet->setName($oldbouquet['bouquet_name']);
            $bouquet->setChanelids(json_decode($oldbouquet['bouquet_channels'], JSON_FORCE_OBJECT));
            $bouquet->setSerieids(json_decode($oldbouquet['bouquet_series'], JSON_FORCE_OBJECT));
            $bouquet->setBouquetorder($oldbouquet['bouquet_order']);
            $this->doctrine->getManager()->flush();
        }
    }
}
