<?php

namespace Backmarket\Command;

use Backmarket\HtmlScapper;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Serializer;

class ProductPriceDownloaderCommand extends Command
{
    /** @var  OutputInterface */
    protected $output;
    /** @var  Serializer */
    protected $serializer;

    protected $productUrlList = [
        'iPhone 6S 64Go Gris' => 'https://www.backmarket.fr/iphone-6s-64-go-gris-sideral-debloque-tout-operateur-pas-cher/3215.html',
        'iPhone 6S 64Go Argent' => 'https://www.backmarket.fr/iphone-6s-64-go-argent-debloque-tout-operateur-pas-cher/3218.html',
        'iPhone 6S 32Go Gris' => 'https://www.backmarket.fr/iphone-6s-32-go-gris-sideral-debloque-tout-operateur-pas-cher/14663.html',
        'iPhone 6S 32Go Argent' => 'https://www.backmarket.fr/iphone-6s-32-go-argent-debloque-tout-operateur-pas-cher/14664.html',
    ];

    protected function configure()
    {
        $this
            ->setName('backmarket:price:downloader');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->serializer = new Serializer([], [new CsvEncoder()]);

        $this->runCom();
    }

    protected function runCom()
    {
        $progress = new ProgressBar($this->output, count($this->productUrlList));
        $progress->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');
        $progress->start();
        $products = [];
        foreach ($this->productUrlList as $productName => $url) {
            $productHtmlPage = $this->getProductPage($url);
            $prices = $this->extractPrices($productHtmlPage);
            $products[$productName] = $prices;

            $progress->advance();
        }

        $this->renderTable($products);


        $progress->finish();
        $this->output->writeln('');

    }

    protected function getProductPage($url)
    {
        $client = new \GuzzleHttp\Client();
        try {
            $response = $client->request('GET', $url);
            $html = $response->getBody()->getContents();
        } catch (ClientException $e) {
            throw new \Exception();
        }

        return $html;
    }

    protected function extractPrices($productHtmlPage)
    {
        $scrapper = new HtmlScapper($productHtmlPage);

        return $scrapper->getFromList([
            ['method' => 'getValue', 'name' => 'Stallone', 'filter' => 'button[data-state=4]', 'attr' => 'data-price'],
            ['method' => 'getValue', 'name' => 'Bronze', 'filter' => 'button[data-state=3]', 'attr' => 'data-price'],
            ['method' => 'getValue', 'name' => 'Argent', 'filter' => 'button[data-state=2]', 'attr' => 'data-price'],
            ['method' => 'getValue', 'name' => 'Or', 'filter' => 'button[data-state=1]', 'attr' => 'data-price'],
            ['method' => 'getValue', 'name' => 'Shiny', 'filter' => 'button[data-state=0]', 'attr' => 'data-price'],
        ]);
    }

    protected function renderTable(array $products)
    {
        $productsForTable = [];

        foreach ($products as $name => $prices) {
            foreach ($prices as $grade => $price) {
                if ((int)$price > 1) {
                    $productsForTable[$price] = [$name, $grade, (int)$price];
                }
            }
        }
        ksort($productsForTable);
        $table = new Table($this->output);
        $table
            ->setHeaders(['Name', 'Grade', 'Price'])
            ->setRows($productsForTable);
        $table->render();
    }
}
