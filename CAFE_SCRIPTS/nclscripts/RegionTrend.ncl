;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;;;;;;;;;;;;;;;;;;;Editor: Eric Xu;;;;;;;;;;;;;;;;;;;;;;;
;;;;;;;;;;;;;;;;;;;;;Date: 2015.04.01;;;;;;;;;;;;;;;;;;;;;;
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
load "$NCARG_ROOT/lib/ncarg/nclscripts/csm/gsn_code.ncl"
load "$NCARG_ROOT/lib/ncarg/nclscripts/csm/gsn_csm.ncl"
load "$NCARG_ROOT/lib/ncarg/nclscripts/csm/contributed.ncl"
load "$NCARG_ROOT/lib/ncarg/nclscripts/csm/shea_util.ncl"
begin
;************************************************
; Define parameters
;************************************************
   if(.not.isvar("fi_data")) then
   	fi_data = "/mnt/CMIP5/cmip5_tmp/1010/year_data"
   end if
   if(.not.isvar("fi_name")) then
   	fi_name = "sic_OImon_FGOALS-g2_historicalGHG_r1i1p1_192701-199112_year.nc"
   end if
   if(.not.isvar("fig_data")) then
   	fig_data = "/mnt/CMIP5/cmip5_tmp/1010/fig_data"
   end if
   if(.not.isvar("fig_name")) then
   	fig_name = "sic_OImon_FGOALS-g2_historicalGHG_r1i1p1_192701-199112_TREND_45_90_160_260"
   end if
   if(.not.isvar("nc_data")) then
   	nc_data = "/mnt/CMIP5/cmip5_tmp/1010/nc_data"
   end if
   if(.not.isvar("nc_name")) then
   	nc_name = "sic_OImon_FGOALS-g2_historicalGHG_r1i1p1_192701-199112_TREND_45_90_160_260.nc"
   end if
   if(.not.isvar("var_name")) then
   	var_name="sic"
   end if
   if(.not.isvar("fig_type")) then
   	fig_type= "png"
   end if
   if(.not.isvar("latmin")) then
   	latmin=45
   end if
   if(.not.isvar("latmax")) then
   	latmax=90
   end if
   if(.not.isvar("lonmin")) then
   	lonmin=160
   end if
   if(.not.isvar("lonmax")) then
   	lonmax=260
   end if

;************************************************
; process netCDF file
;************************************************

   fi  =   addfile(fi_data+"/"+fi_name,"r") 
   var =   fi->$var_name$
   var_reordered=var(time|:,lat|:,lon|:)
   dat = var_reordered(:,{latmin:latmax},{lonmin:lonmax})
   dat = where(ismissing(dat),0,dat)
   
   TIME   = fi->time
   YYYY   = cd_calendar(TIME,-1)/100 
   dim = dimsizes(dat)
   
   rc = regCoef(TIME,dat(lat|:,lon|:,time|:))
   printVarSummary(rc)
   copy_VarMeta(dat(0,:,:),rc)

;************************************************
; creating plot
;************************************************
    colorbar    =   "BlueDarkRed18"
    wks = gsn_open_wks(fig_type, fig_data+"/"+fig_name) 
    gsn_define_colormap(wks,colorbar)
    res =   True
    res@gsnMaximize     =   True
    res@gsnScale  = True
    res@gsnAddCyclic         = False

    res@tiMainString        =       "Trend of "+var@long_name+": "+tostring(min(YYYY))+"-"+tostring(max(YYYY))
    res@tiXAxisString       =       ""
    res@tiYAxisString       =       ""
    res@gsnCenterString     =       "" 
    res@gsnLeftString = "slope"
    res@gsnRightString =""
    res@tiMainFontHeightF   =       0.024
    res@tiMainFont          =       21
    res@gsnLeftStringFontHeightF    =       0.02
    res@gsnRightStringFontHeightF   =       0.02
    res@gsnCenterStringFontHeightF  =       0.023
    res@mpGeophysicalLineThicknessF =2
    res@mpFillDrawOrder      = "PostDraw"

    res@cnFillOn             = True                 ; turn on color
    res@cnLinesOn            = False        ; True is default
    res@cnLineLabelsOn       = False        ; True is default
    

    res@gsnSpreadColors      = True                 ; use full colormap
    res@mpLabelFontHeightF      =   0.03

    res@pmLabelBarWidthF        =   0.8 
    res@pmLabelBarHeightF       =   0.1 
    res@pmLabelBarOrthogonalPosF    =   0.15
    res@cnMaxLevelCount      = 50

    res@mpCenterLonF         = (lonmin+lonmax)/2
    res@mpMinLatF            = latmin         ; zoom in on map
    res@mpMaxLatF            = latmax
    res@mpMinLonF            = lonmin
    res@mpMaxLonF            = lonmax
    symMinMaxPlt(rc, 16, False, res);
    plot    =   gsn_csm_contour_map_ce(wks,rc,res)

  draw(plot)
;*******************************************
; export Trend nc data
;*******************************************
  system("rm -f " + nc_data+"/"+nc_name)
  fout= addfile(nc_data+"/"+nc_name,"c")
  setfileoption(fout,"DefineMode",True)
  
  fAtt               = True            ; assign file attributes
  fAtt@title         = "Trend of "+var@long_name+": "+tostring(min(YYYY))+"-"+tostring(max(YYYY)) 
  fAtt@source_file   = fi_name
  fAtt@Conventions   = "None"   
  fAtt@creation_date = systemfunc ("date")        
  fileattdef( fout, fAtt )            ; copy file attributes  
  nlon=dimsizes(rc&lon)
  nlat=dimsizes(rc&lat)
  dimNames = (/"lat", "lon"/)  
  dimSizes = (/nlat,  nlon/)
  dimUnlim = (/False, False/)
  print(dimSizes)
  filedimdef(fout,dimNames,dimSizes,dimUnlim)

  filevardef(fout, "lat"  ,typeof(rc&lat),getvardims(rc&lat))
  filevardef(fout, "lon"  ,typeof(rc&lon),getvardims(rc&lon))
  filevardef(fout, "rc"  ,typeof(rc)  ,getvardims(rc))

  filevarattdef(fout,"lat"  ,rc&lat)
  filevarattdef(fout,"lon"  ,rc&lon)
  filevarattdef(fout,"rc"  ,rc)

  setfileoption(fout,"DefineMode",False)

  fout->lat    = (/rc&lat/)
  fout->lon    = (/rc&lon/)
  fout->rc      = (/rc/)
end

