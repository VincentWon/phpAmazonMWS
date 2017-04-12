<?php

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2012-12-12 at 13:17:14.
 */
class AmazonReportTest extends PHPUnit_Framework_TestCase {

    /**
     * @var AmazonReport
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        resetLog();
        $this->object = new AmazonReport('testStore', null, true, null, __DIR__.'/../../test-config.php');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }
    
    public function testSetUp(){
        $obj = new AmazonReport('testStore', '77', true, null, __DIR__.'/../../test-config.php');
        
        $o = $obj->getOptions();
        $this->assertArrayHasKey('ReportId',$o);
        $this->assertEquals('77', $o['ReportId']);
    }
    
    public function testSetReportId(){
        $this->assertNull($this->object->setReportId(777));
        $o = $this->object->getOptions();
        $this->assertArrayHasKey('ReportId',$o);
        $this->assertEquals(777,$o['ReportId']);
        $this->assertNull($this->object->setReportId('777')); //works for number strings
        $this->assertFalse($this->object->setReportId('five')); //but not other strings
        $this->assertFalse($this->object->setReportId(null)); //won't work for other things
    }
    
    public function testFetchReport(){
        resetLog();
        $this->object->setMock(true,'fetchReport.xml');
        
        $this->assertFalse($this->object->fetchReport()); //no report ID set yet
        
        $this->object->setReportId('777');
        $ok = $this->object->fetchReport(); //now it is good
        $this->assertNull($ok);
        
        $o = $this->object->getOptions();
        $this->assertEquals('GetReport',$o['Action']);
        
        $check = parseLog();
        $this->assertEquals('Single Mock File set: fetchReport.xml',$check[1]);
        $this->assertEquals('Log ID must be set in order to fetch it!',$check[2]);
        $this->assertEquals('Fetched Mock File: mock/fetchReport.xml',$check[3]);
        
        return $this->object;
        
    }
    
    /**
     * @depends testFetchReport
     */
    public function testSaveReport($o){
        $path = __DIR__.'/../../mock/saveReport.xml';
        $path2 = __DIR__.'/../../mock/fetchReport.xml';
        $o->saveReport($path);
        $check = parseLog();
        $this->assertEquals("Successfully saved report #777 at $path",$check[1]);
        $this->assertFileEquals($path2, $path);
        $this->assertFalse($this->object->saveReport('here')); //not fetched yet for this object
    }

    /**
     * @depends testFetchReport
     * @param AmazonReport $o
     */
    public function testGetRawReport($o) {
        $this->assertEquals('This is a report.', $o->getRawReport());

        $this->assertFalse($this->object->getRawReport()); //not fetched yet for this object
    }
    
}

require_once('helperFunctions.php');