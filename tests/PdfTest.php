<?php
namespace tests;

use PHPUnit\Framework\TestCase;
use mikehaertl\wkhtmlto\Pdf;
use mikehaertl\tmp\File;

class PdfTest extends TestCase
{
    CONST URL = 'http://www.google.com/robots.txt';

    // Create PDF through constructor
    public function testCanCreatePdfFromFile()
    {
        $inFile = $this->getHtmlAsset();
        $outFile = $this->getOutFile();
        $binary = $this->getBinary();

        $pdf = new Pdf($inFile);
        $pdf->binary = $binary;
        $this->assertTrue($pdf->saveAs($outFile));
        $this->assertFileExists($outFile);

        $tmpFile = $pdf->getPdfFilename();
        $this->assertEquals("$binary '$inFile' '$tmpFile'", (string) $pdf->getCommand());
        unlink($outFile);
    }
    public function testCanCreatePdfFromHtmlString()
    {
        $outFile = $this->getOutFile();
        $binary = $this->getBinary();

        $pdf = new Pdf('<html><h1>Test</h1></html>');
        $pdf->binary = $binary;
        $this->assertTrue($pdf->saveAs($outFile));
        $this->assertFileExists($outFile);

        $tmpFile = $pdf->getPdfFilename();
        $regex = "#$binary '[^ ]+' '$tmpFile'#";
        $command = (string) $pdf->getCommand();
        if (phpUnitVersion('<', 9)) {
            $this->assertRegExp($regex, $command);
        } else {
            $this->assertMatchesRegularExpression($regex, $command);
        }
        unlink($outFile);
    }
    public function testCanCreatePdfFromXmlString()
    {
        $outFile = $this->getOutFile();
        $binary = $this->getBinary();

        $pdf = new Pdf('<?xml version="1.0" encoding="UTF-8" standalone="no"?><svg xmlns="http://www.w3.org/2000/svg" version="1.0"></svg>');
        $pdf->binary = $binary;
        $this->assertTrue($pdf->saveAs($outFile));
        $this->assertFileExists($outFile);

        $tmpFile = $pdf->getPdfFilename();
        $regex = "#$binary '[^ ]+' '$tmpFile'#";
        $command = (string) $pdf->getCommand();
        if (phpUnitVersion('<', 9)) {
            $this->assertRegExp($regex, $command);
        } else {
            $this->assertMatchesRegularExpression($regex, $command);
        }
        unlink($outFile);
    }
    public function testCanCreatePdfFromUrl()
    {
        $url = self::URL;
        $outFile = $this->getOutFile();
        $binary = $this->getBinary();

        $pdf = new Pdf($url);
        $pdf->binary = $binary;
        $this->assertTrue($pdf->saveAs($outFile));
        $this->assertFileExists($outFile);

        $tmpFile = $pdf->getPdfFilename();
        $this->assertEquals("$binary '$url' '$tmpFile'", (string) $pdf->getCommand());
        unlink($outFile);
    }


    // Add pages
    public function testCanAddPagesFromFile()
    {
        $inFile = $this->getHtmlAsset();
        $outFile = $this->getOutFile();
        $binary = $this->getBinary();

        $pdf = new Pdf;
        $pdf->binary = $binary;
        $this->assertInstanceOf('mikehaertl\wkhtmlto\Pdf', $pdf->addPage($inFile));
        $this->assertInstanceOf('mikehaertl\wkhtmlto\Pdf', $pdf->addPage($inFile));
        $this->assertTrue($pdf->saveAs($outFile));

        $tmpFile = $pdf->getPdfFilename();
        $command = (string)$pdf->getCommand();
        $this->assertEquals("$binary '$inFile' '$inFile' '$tmpFile'", $command);
        unlink($outFile);
    }
    public function testCanAddPagesFromHtmlString()
    {
        $outFile = $this->getOutFile();
        $binary = $this->getBinary();

        $pdf = new Pdf;
        $pdf->binary = $binary;
        $this->assertInstanceOf('mikehaertl\wkhtmlto\Pdf', $pdf->addPage('<html><h1>Test</h1></html>'));
        $this->assertInstanceOf('mikehaertl\wkhtmlto\Pdf', $pdf->addPage('<html><h1>Test</h1></html>'));
        $this->assertTrue($pdf->saveAs($outFile));
        $this->assertFileExists($outFile);

        $tmpFile = $pdf->getPdfFilename();
        $regex = "#$binary '[^ ]+' '[^ ]+' '$tmpFile'#";
        $command = (string) $pdf->getCommand();
        if (phpUnitVersion('<', 9)) {
            $this->assertRegExp($regex, $command);
        } else {
            $this->assertMatchesRegularExpression($regex, $command);
        }
        unlink($outFile);
    }
    public function testCanAddPagesFromUrl()
    {
        $url = self::URL;
        $outFile = $this->getOutFile();
        $binary = $this->getBinary();

        $pdf = new Pdf;
        $pdf->binary = $binary;
        $this->assertInstanceOf('mikehaertl\wkhtmlto\Pdf', $pdf->addPage($url));
        $this->assertInstanceOf('mikehaertl\wkhtmlto\Pdf', $pdf->addPage($url));
        $this->assertTrue($pdf->saveAs($outFile));
        $this->assertFileExists($outFile);

        $tmpFile = $pdf->getPdfFilename();
        $this->assertEquals("$binary '$url' '$url' '$tmpFile'", (string) $pdf->getCommand());
        unlink($outFile);
    }
    public function testCanAddPageFromHtmlString()
    {
        $outFile = $this->getOutFile();
        $binary = $this->getBinary();

        $pdf = new Pdf;
        $pdf->binary = $binary;
        $pdf->addPage('<html><h1>test</h1></html>');
        $pdf->saveAs($outFile);
        $regex = "/tmp_wkhtmlto_pdf_.*?\.html/";
        $command = (string) $pdf->getCommand()->getExecCommand();
        if (phpUnitVersion('<', 9)) {
            $this->assertRegExp($regex, $command);
        } else {
            $this->assertMatchesRegularExpression($regex, $command);
        }
        unlink($outFile);
    }
    public function testCanAddPageFromFileInstance()
    {
        $outFile = $this->getOutFile();
        $binary = $this->getBinary();

        $pdf = new Pdf;
        $pdf->binary = $binary;
        $pdf->addPage(new File('Some content', '.html'));
        $pdf->saveAs($outFile);
        $regex = "/php_tmpfile_.*?\.html/";
        $command = (string) $pdf->getCommand()->getExecCommand();
        if (phpUnitVersion('<', 9)) {
            $this->assertRegExp($regex, $command);
        } else {
            $this->assertMatchesRegularExpression($regex, $command);
        }
        unlink($outFile);
    }
    public function testCanAddPageFromXmlString()
    {
        $outFile = $this->getOutFile();
        $binary = $this->getBinary();

        $pdf = new Pdf;
        $pdf->binary = $binary;
        $pdf->addPage('<xml>test</xml>');
        $pdf->saveAs($outFile);
        $regex = "/tmp_wkhtmlto_pdf_.*?\.xml/";
        $command = (string) $pdf->getCommand()->getExecCommand();
        if (phpUnitVersion('<', 9)) {
            $this->assertRegExp($regex, $command);
        } else {
            $this->assertMatchesRegularExpression($regex, $command);
        }
        unlink($outFile);
    }
    public function testCanAddHtmlPageFromStringByType()
    {
        $outFile = $this->getOutFile();
        $binary = $this->getBinary();

        $pdf = new Pdf;
        $pdf->binary = $binary;
        $pdf->addPage('Test', array(), Pdf::TYPE_HTML);
        $pdf->saveAs($outFile);
        $regex = "/tmp_wkhtmlto_pdf_.*?\.html/";
        $command = (string) $pdf->getCommand()->getExecCommand();
        if (phpUnitVersion('<', 9)) {
            $this->assertRegExp($regex, $command);
        } else {
            $this->assertMatchesRegularExpression($regex, $command);
        }
        unlink($outFile);
    }
    public function testCanAddXmlPageFromStringByType()
    {
        $outFile = $this->getOutFile();
        $binary = $this->getBinary();

        $pdf = new Pdf;
        $pdf->binary = $binary;
        $pdf->addPage('Test', array(), Pdf::TYPE_XML);
        $pdf->saveAs($outFile);
        $regex = "/tmp_wkhtmlto_pdf_.*?\.xml/";
        $command = (string) $pdf->getCommand()->getExecCommand();
        if (phpUnitVersion('<', 9)) {
            $this->assertRegExp($regex, $command);
        } else {
            $this->assertMatchesRegularExpression($regex, $command);
        }
        unlink($outFile);
    }



    // Cover page
    public function testCanAddCoverFromFile()
    {
        $inFile = $this->getHtmlAsset();
        $outFile = $this->getOutFile();
        $binary = $this->getBinary();

        $pdf = new Pdf;
        $pdf->binary = $binary;
        $this->assertInstanceOf('mikehaertl\wkhtmlto\Pdf', $pdf->addCover($inFile));
        $this->assertTrue($pdf->saveAs($outFile));

        $tmpFile = $pdf->getPdfFilename();
        $command = (string)$pdf->getCommand();
        $this->assertEquals("$binary 'cover' '$inFile' '$tmpFile'", $command);
        unlink($outFile);
    }
    public function testCanAddCoverFromHtmlString()
    {
        $outFile = $this->getOutFile();
        $binary = $this->getBinary();

        $pdf = new Pdf;
        $pdf->binary = $binary;
        $this->assertInstanceOf('mikehaertl\wkhtmlto\Pdf', $pdf->addCover('<html><h1>Test</h1></html>'));
        $this->assertTrue($pdf->saveAs($outFile));
        $this->assertFileExists($outFile);

        $tmpFile = $pdf->getPdfFilename();
        $regex = "#$binary 'cover' '[^ ]+' '$tmpFile'#";
        $command = (string) $pdf->getCommand();
        if (phpUnitVersion('<', 9)) {
            $this->assertRegExp($regex, $command);
        } else {
            $this->assertMatchesRegularExpression($regex, $command);
        }
        unlink($outFile);
    }
    public function testCanAddCoverFromUrl()
    {
        $url = self::URL;
        $outFile = $this->getOutFile();
        $binary = $this->getBinary();

        $pdf = new Pdf;
        $pdf->binary = $binary;
        $this->assertInstanceOf('mikehaertl\wkhtmlto\Pdf', $pdf->addCover($url));
        $this->assertTrue($pdf->saveAs($outFile));
        $this->assertFileExists($outFile);

        $tmpFile = $pdf->getPdfFilename();
        $this->assertEquals("$binary 'cover' '$url' '$tmpFile'", (string) $pdf->getCommand());
        unlink($outFile);
    }

    // Add Toc
    public function testCanAddToc()
    {
        $outFile = $this->getOutFile();
        $binary = $this->getBinary();

        $pdf = new Pdf('<html><h1>Test</h1></html>');
        $pdf->binary = $binary;
        $this->assertInstanceOf('mikehaertl\wkhtmlto\Pdf', $pdf->addToc());
        $this->assertTrue($pdf->saveAs($outFile));
        $this->assertFileExists($outFile);

        $tmpFile = $pdf->getPdfFilename();
        $regex = "#$binary '[^ ]+' 'toc' '$tmpFile'#";
        $command = (string) $pdf->getCommand();
        if (phpUnitVersion('<', 9)) {
            $this->assertRegExp($regex, $command);
        } else {
            $this->assertMatchesRegularExpression($regex, $command);
        }
        unlink($outFile);
    }

    public function testToString()
    {
        $outFile = $this->getOutFile();
        $binary = $this->getBinary();

        $pdf = new Pdf('<html><h1>Test</h1></html>');
        $pdf->binary = $binary;

        $this->assertTrue($pdf->saveAs($outFile));
        $this->assertFileExists($outFile);

        $this->assertEquals(file_get_contents($outFile), $pdf->toString());
        unlink($outFile);
    }

    // Options
    public function testCanPassGlobalOptionsInConstructor()
    {
        $inFile = $this->getHtmlAsset();
        $outFile = $this->getOutFile();
        $binary = $this->getBinary();
        $tmpDir = realpath(sys_get_temp_dir()) . DIRECTORY_SEPARATOR . uniqid();
        mkdir($tmpDir);

        $pdf = new Pdf(array(
            'binary' => $binary,
            'tmpDir' => $tmpDir,
            'header-html' => '<p>header</p>',
            'no-outline',
            'margin-top'    => 0,
            'allow' => array(
                '/tmp',
                '/test',
            ),
        ));
        $this->assertInstanceOf('mikehaertl\wkhtmlto\Pdf', $pdf->addPage($inFile));
        $this->assertEquals($tmpDir, $pdf->tmpDir);
        $this->assertTrue($pdf->saveAs($outFile));

        $this->assertFileExists($outFile);
        $regex = "#$binary '--header-html' '$tmpDir/tmp_wkhtmlto_pdf_[^ ]+\.html' '--no-outline' '--margin-top' '0' '--allow' '/tmp' '--allow' '/test' '$inFile' '$tmpDir/tmp_wkhtmlto_pdf_[^ ]+\.pdf'#";
        $command = (string) $pdf->getCommand();
        if (phpUnitVersion('<', 9)) {
            $this->assertRegExp($regex, $command);
        } else {
            $this->assertMatchesRegularExpression($regex, $command);
        }
        unlink($outFile);
    }
    public function testCanSetGlobalOptions()
    {
        $inFile = $this->getHtmlAsset();
        $outFile = $this->getOutFile();
        $binary = $this->getBinary();

        $pdf = new Pdf;
        $pdf->setOptions(array(
            'binary' => $binary,
            'no-outline',
            'margin-top'    => 0,
        ));
        $this->assertInstanceOf('mikehaertl\wkhtmlto\Pdf', $pdf->addPage($inFile));
        $this->assertTrue($pdf->saveAs($outFile));
        $this->assertFileExists($outFile);

        $tmpFile = $pdf->getPdfFilename();
        $this->assertEquals("$binary '--no-outline' '--margin-top' '0' '$inFile' '$tmpFile'", (string) $pdf->getCommand());
        unlink($outFile);
    }

    public function testCanDisableSmartShirinkingOption()
    {
        $inFile = $this->getHtmlAsset();
        $outFile = $this->getOutFile();
        $binary = $this->getBinary();

        $pdf = new Pdf;
        $pdf->setOptions(array(
            'binary' => $binary,
            'enable-smart-shrinking'
        ));
        $pdf->disableSmartShrinking();
        $this->assertInstanceOf('mikehaertl\wkhtmlto\Pdf', $pdf->addPage($inFile));
        $this->assertTrue($pdf->saveAs($outFile));
        $this->assertFileExists($outFile);

        $tmpFile = $pdf->getPdfFilename();
        $this->assertEquals("$binary '--disable-smart-shrinking' '$inFile' '$tmpFile'", (string) $pdf->getCommand());
        unlink($outFile);
    }

    public function testSetPageCoverAndTocOptions()
    {
        $inFile = $this->getHtmlAsset();
        $outFile = $this->getOutFile();
        $binary = $this->getBinary();

        $pdf = new Pdf(array(
            'binary' => $binary,
            'no-outline',
            'margin-top'    => 0,
            'header-center' => 'test {x} {y}',
        ));
        $this->assertInstanceOf('mikehaertl\wkhtmlto\Pdf', $pdf->addPage($inFile, array(
            'no-background',
            'zoom' => 1.5,
            'cookie' => array('name'=>'value'),
            'replace' => array(
                'x' => 'v',
                'y' => '',
            ),
        )));
        $this->assertInstanceOf('mikehaertl\wkhtmlto\Pdf', $pdf->addCover($inFile, array(
            'replace' => array(
                'x' => 'a',
                'y' => 'b',
            ),
        )));
        $this->assertInstanceOf('mikehaertl\wkhtmlto\Pdf', $pdf->addToc(array(
            'disable-dotted-lines'
        )));
        $this->assertTrue($pdf->saveAs($outFile));
        $this->assertFileExists($outFile);

        $tmpFile = $pdf->getPdfFilename();
        $this->assertEquals("$binary '--no-outline' '--margin-top' '0' '--header-center' 'test {x} {y}' '$inFile' '--no-background' '--zoom' '1.5' '--cookie' 'name' 'value' '--replace' 'x' 'v' '--replace' 'y' '' 'cover' '$inFile' '--replace' 'x' 'a' '--replace' 'y' 'b' 'toc' '--disable-dotted-lines' '$tmpFile'", (string) $pdf->getCommand());
        unlink($outFile);
    }
    public function testCanAddHeaderAndFooterAsHtml()
    {
        $inFile = $this->getHtmlAsset();
        $outFile = $this->getOutFile();
        $binary = $this->getBinary();

        $pdf = new Pdf(array(
            'binary' => $binary,
            'header-html' => '<h1>Header</h1>',
            'footer-html' => '<h1>Footer</h1>',
        ));
        $this->assertInstanceOf('mikehaertl\wkhtmlto\Pdf', $pdf->addPage($inFile));
        $this->assertTrue($pdf->saveAs($outFile));
        $this->assertFileExists($outFile);

        $tmpFile = $pdf->getPdfFilename();
        $regex = "#$binary '--header-html' '/tmp/[^ ]+' '--footer-html' '/tmp/[^ ]+' '$inFile' '$tmpFile'#";
        $command = (string) $pdf->getCommand();
        if (phpUnitVersion('<', 9)) {
            $this->assertRegExp($regex, $command);
        } else {
            $this->assertMatchesRegularExpression($regex, $command);
        }
        unlink($outFile);
    }
    public function testCanAddHeaderAndFooterAsFile()
    {
        $inFile = $this->getHtmlAsset();
        $outFile = $this->getOutFile();
        $binary = $this->getBinary();

        $pdf = new Pdf(array(
            'binary' => $binary,
            'header-html' => new File('Some header content', '.html'),
            'footer-html' => new File('Some footer content', '.html'),
        ));
        $this->assertInstanceOf('mikehaertl\wkhtmlto\Pdf', $pdf->addPage($inFile));
        $this->assertTrue($pdf->saveAs($outFile));
        $this->assertFileExists($outFile);

        $tmpFile = $pdf->getPdfFilename();
        $regex = "#$binary '--header-html' '/tmp/[^ ]+' '--footer-html' '/tmp/[^ ]+' '$inFile' '$tmpFile'#";
        $command = (string) $pdf->getCommand();
        if (phpUnitVersion('<', 9)) {
            $this->assertRegExp($regex, $command);
        } else {
            $this->assertMatchesRegularExpression($regex, $command);
        }
        unlink($outFile);
    }
    public function testCanAddHeaderAndFooterAsHtmlToPagesAndCoverAndToc()
    {
        $inFile = $this->getHtmlAsset();
        $outFile = $this->getOutFile();
        $binary = $this->getBinary();

        $pdf = new Pdf(array(
            'binary' => $binary,
        ));
        $this->assertInstanceOf('mikehaertl\wkhtmlto\Pdf', $pdf->addPage('<html>test</html>', array(
            'header-html' => '<h1>Page Header</h1>',
            'footer-html' => '<h1>Page Footer</h1>',
        )));
        $this->assertInstanceOf('mikehaertl\wkhtmlto\Pdf', $pdf->addCover($inFile, array(
            'header-html' => '<h1>Cover Header</h1>',
            'footer-html' => '<h1>Cover Footer</h1>',
        )));
        $this->assertInstanceOf('mikehaertl\wkhtmlto\Pdf', $pdf->addToc(array(
            'header-html' => '<h1>Toc Header</h1>',
            'footer-html' => '<h1>Toc Footer</h1>',
        )));
        $this->assertTrue($pdf->saveAs($outFile));
        $this->assertFileExists($outFile);

        $tmpFile = $pdf->getPdfFilename();
        $regex = "#$binary '/tmp/[^ ]+\.html' '--header-html' '/tmp/[^ ]+\.html' '--footer-html' '/tmp/[^ ]+\.html' 'cover' '$inFile' '--header-html' '/tmp/[^ ]+\.html' '--footer-html' '/tmp/[^ ]+\.html' 'toc' '--header-html' '/tmp/[^ ]+\.html' '--footer-html' '/tmp/[^ ]+\.html' '$tmpFile'#";
        $command = (string) $pdf->getCommand();
        if (phpUnitVersion('<', 9)) {
            $this->assertRegExp($regex, $command);
        } else {
            $this->assertMatchesRegularExpression($regex, $command);
        }
        unlink($outFile);
    }


    // Xvfb
    public function testCanUseXvfbRun()
    {
        $inFile = $this->getHtmlAsset();
        $outFile = $this->getOutFile();
        $binary = $this->getBinary();

        $pdf = new Pdf(array(
            'binary' => $binary,
            'commandOptions' => array(
                'enableXvfb' => true,
            ),
        ));

        $this->assertInstanceOf('mikehaertl\wkhtmlto\Pdf', $pdf->addPage($inFile));
        $this->assertTrue($pdf->saveAs($outFile));

        $tmpFile = $pdf->getPdfFilename();
        $this->assertEquals("xvfb-run -a --server-args=\"-screen 0, 1024x768x24\" $binary '$inFile' '$tmpFile'", (string) $pdf->getCommand());
        unlink($outFile);
    }


    protected function getBinary()
    {
        return '/usr/local/bin/wkhtmltopdf';
    }

    protected function getHtmlAsset()
    {
        return __DIR__.'/assets/test.html';
    }

    protected function getOutFile()
    {
        return __DIR__.'/test.pdf';
    }
}
