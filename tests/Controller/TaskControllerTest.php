<?php

namespace App\Tests\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    
    public function testIndex()
    {
        $client = static::createClient();
        $client->request('GET', '/tasks');        
        $this->assertEquals(200, $client->getResponse()->getStatusCode());      
    }         
    
    public function testAddOrEdit()
    {
        $client = static::createClient();
        $client->request('POST', '/task/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
    public function testDelete() { 
        $client = static::createClient();
        $client->request('DELETE', '/task/delete/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode()); 
    }
   
}
