# CAFE_SCRIPTS
#####Analytic scripts are essential part of CAFE node package.        
You could find the file `/config/src/main/resources/baseResources/config.properties`       
The paremeter `ScriptFolder` defines the path of analytic scripts.       
You have to place the `nclscripts` under the `ScriptFolder`      
we have now absorbed five representative analytic functions as follows.        
Some functions can set the map projection to North Hemisphere(NH), South Hemisphere(SH) or a specific area.
#####1.	Empirical Orthogonal Function(EOF) analysis (specific area/NH/SH). 
Computes empirical orthogonal functions with the time series of the amplitudes associated with each eigenvalue.      
`RegionEOF.ncl`   `PolarNHEOF.ncl`  `PolarSHEOF.ncl`
#####2.	Long Term Mean (LTM) analysis (specific area/NH/SH).
Averages record variables across a long time period.     
`RegionLTM.ncl`   `PolarNHLTM.ncl`  `PolarSHLTM.ncl`
#####3.	Trend analysis (specific area/NH/SH).
Computes the slope of each grid across a long time period.    
`RegionTrend.ncl`    `PolarNHTrend.ncl`    `PolarSHTrend.ncl`
#####4.	Seasonal contour (NH/SH)
Computes the long term average for four seasons.     
`SeasonalContourNH.ncl`    `SeasonalContourSH.ncl`
#####5.	Time series (Annual/Seasonal)
Computes the time series and linear fit function by season or the whole year.     
`SeasonalTS.ncl`    `AnnualTS.ncl`
