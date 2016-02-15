<?php
class EnumData{
	public static function NCL_Types(){
		return array(
				'RegionLTM' => 'Long Term Mean(Specified Region)',
				'PolarNHLTM' => 'Long Term Mean(Polar Map NH)',
				'PolarSHLTM' => 'Long Term Mean(Polar Map SH)',
				'RegionTrend' => 'Trend Analysis(Specified Region)',
				'PolarNHTrend' => 'Trend Analysis(Polar Map NH)',
				'PolarSHTrend' => 'Trend Analysis(Polar Map SH)',
				'RegionEOF' => 'EOF Analysis(Specified Region)',
				'PolarNHEOF' => 'EOF Analysis(Polar Map NH)',
				'PolarSHEOF' => 'EOF Analysis(Polar Map SH)',
				'AnnualTS' => 'Time Series(Annual)',
				'SeasonalTS' => 'Time Series(Seasonal)',
                                'SeasonalContourNH' => 'Seasonal Cycle(Polar Map NH)',
                                'SeasonalContourSH' => 'Seasonal Cycle(Polar Map SH)',
		);
	}
}
