<?php

namespace Backmarket;

use Symfony\Component\DomCrawler\Crawler;

/**
 * <tag attribute="value">content</tag>
 *
 * @author Léo Benoist
 */
class HtmlScapper
{
    /** @var  Crawler */
    protected $crawler;

    protected $html;

    /**
     * HtmlScapper constructor.
     */
    public function __construct($html)
    {
        $this->crawler = new Crawler($html);
        $this->html = $html;
    }

    public function getFromList(array $list)
    {
        $crawled = [];

        foreach ($list as $parameters) {
            switch ($parameters['method']) {
                case 'getValue':
                    $crawled[$parameters['name']] = $this->getValue($parameters['filter'], $parameters['attr']);
                    break;
                case 'getValues':
                    $crawled[$parameters['name']] = $this->getValues($parameters['filter'], $parameters['attr']);
                    break;
                case 'getContent':
                    $crawled[$parameters['name']] = $this->getContent($parameters['filter']);
                    break;
                case 'getFromCallback':
                    $crawled[$parameters['name']] = $this->getFromCallback($parameters['callback'], $crawled);
                    break;
                case 'getTable':
                    $this->getTable($parameters['filter'], $crawled);
                    break;
                case 'remove':
                    unset($crawled[$parameters['field']]);
                    break;
                case 'copy':
                    $crawled[$parameters['name']] = $crawled[$parameters['field']];
                    break;
                case 'rename':
                    if (!isset($crawled[$parameters['from']])) {
                        break;
                    }
                    $crawled[$parameters['to']] = $crawled[$parameters['from']];
                    unset($crawled[$parameters['from']]);

                    break;
                case 'exist':
                    $crawled[$parameters['name']] = $this->exist($parameters['filter']);
                    break;
                default:
                    throw new \Exception('crawler method unknow');
            }
        }

        return $crawled;
    }

    /**
     * <tag attribute="What you want"/>
     *
     * @param string $filter
     * @param string $attribute
     *
     * @return string
     */
    public function getValue($filter, $attribute)
    {
        return str_replace([PHP_EOL, ';'], ' ', $this->crawler->filter($filter)->attr($attribute));
    }

    /**
     * @param string $filter
     * @param string $attribute
     *
     * @return array
     */
    public function getValues($filter, $attribute)
    {
        return $this->crawler->filter($filter)->each(function ($subCrawler, $i) use ($attribute) {
            return $subCrawler->attr($attribute);
        });
    }

    /**
     * <tag>What you want</tag>
     *
     * @param string $filter
     *
     * @return string
     */
    public function getContent($filter)
    {
        return str_replace([PHP_EOL, ';'], ' ', $this->crawler->filter($filter)->html());
    }

    /**
     * @param string $filter
     *
     * @return array
     */
    public function getContents($filter)
    {
        return $this->crawler->filter($filter)->each(function ($subCrawler, $i) {
            return $subCrawler->html();
        });
    }

    public function getTable($filter, &$crawled)
    {
        $TRelements = $this->crawler->filter($filter);
        foreach ($TRelements as $i => $content) {
            $crawler = new Crawler($content);
            $crawled[$crawler->filter('td')->getNode(0)->textContent] =
                $crawler->filter('td')->getNode(1)->textContent;
        }
    }

    public function getFromCallback(callable $callback, array &$crawled, array $parameters = [])
    {
        return $callback($crawled, $parameters);
    }


    public function exist($filter)
    {
        return (bool)$this->crawler->filter($filter)->count();
    }
}