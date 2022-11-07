<?php
namespace Trackshift\Test\Upload;

use Trackshift\Upload\PRSStatementUpload;
use Trackshift\Usage\Usage;

class PRSStatementUploadTest extends UploadTestCase {
	public function testGetUsageTotal():void {
		$fileContent = <<<DATA
		Record Number,Work Title,Amount (performance revenue)
		1,Song 1,0.0016
		2,Song 1,0.0016
		3,Song 1,0.0072
		4,Song 2,0.0036
		5,Song 2,0.0048
		6,Song 3,0.0081
		7,Song 3,0.0005
		8,Song 3,0.0006
		9,Song 3,0.0002
		DATA;
		$tmpFileName = self::getTempFile($fileContent);
		$sut = new PRSStatementUpload($tmpFileName);
		$moneyTotalUsage = $sut->getUsageTotal();

		self::assertSame(0.0282, $moneyTotalUsage->value);
		self::assertSame("£0.02", (string)$moneyTotalUsage);
	}

	public function testGetAggregatedUsageTotals():void {
		$fileContent = <<<DATA
		Record Number,Work Title,Amount (performance revenue)
		1,Song 1,0.0016
		2,Song 1,0.0016
		3,Song 1,0.0072
		4,Song 2,0.0036
		5,Song 2,0.0048
		6,Song 3,0.0081
		7,Song 3,0.0005
		8,Song 3,0.0006
		9,Song 3,0.0002
		DATA;
		$tmpFileName = self::getTempFile($fileContent);
		$sut = new PRSStatementUpload($tmpFileName);
		$aggregation = $sut->getAggregatedUsages("workTitle");

		self::assertSame(0.0104, $aggregation->getTotalValueForAggregate("Song 1")->value);
		self::assertSame(0.0084, $aggregation->getTotalValueForAggregate("Song 2")->value);
		self::assertSame(0.0094, $aggregation->getTotalValueForAggregate("Song 3")->value);

		self::assertSame(0.0282, $aggregation->getTotalValue()->value);
	}
}
