;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;;;;;;;;;;;;;;;;;;;Editor: Eric Xu;;;;;;;;;;;;;;;;;;;;;;;
;;;;;;;;;;;;;;;;;;;;;Date: 2015.04.03;;;;;;;;;;;;;;;;;;;;;;
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
   	fi_data = "/mnt/CMIP5/cmip5_tmp/1010/mean_data"
   end if
   if(.not.isvar("fi_name")) then
   	fi_name = "sic_OImon_FGOALS-g2_historicalGHG_r1i1p1_192701-199112_mean.nc"
   end if
   if(.not.isvar("fig_data")) then
   	fig_data = "/mnt/CMIP5/cmip5_tmp/1010/fig_data"
   end if
   if(.not.isvar("fig_name")) then
   	fig_name = "sic_OImon_FGOALS-g2_historicalGHG_r1i1p1_192701-199112_LTM_NH_-90_-45"
   end if
   if(.not.isvar("var_name")) then
   	var_name="sic"
   end if
   if(.not.isvar("fig_type")) then
   	fig_type= "png"
   end if
   if(.not.isvar("latmin")) then
   	latmin=-90
   end if
   if(.not.isvar("latmax")) then
   	latmax=-45
   end if
   if(.not.isvar("start_year")) then
   	start_year=1927
   end if
   if(.not.isvar("end_year")) then
   	end_year=1991
   end if
   
;************************************************
; read in netCDF file
;************************************************
    fi  =   addfile(fi_data+"/"+fi_name,"r") 
    var =   fi->$var_name$
    var_reordered=var(time|:,lat|:,lon|:) 
    dat = var_reordered(0,{latmin:latmax},:)
   ; dat = tmp
    ;dat = mask(tmp,(tmp.ne.0),True)  
    printVarSummary(dat)
    wks = gsn_open_wks(fig_type, fig_data+"/"+fig_name)
    colorbar    =   "WhiteBlueGreenYellowRed"
;************************************************
; create plot
;************************************************ 
    res =   True
    res@gsnMaximize     =   True
    res@gsnScale  = True
    res@gsnAddCyclic         = False

    res@tiMainString        =       start_year +" to "+end_year+" Long Term Mean of "+var@long_name+ " ("+tostring(-latmax)+"S - "+tostring(-latmin)+"S)"
    res@tiXAxisString       =       ""
    res@tiYAxisString       =       ""
    res@gsnCenterString     =       "" 
    res@tiMainFontHeightF   =       0.024
    res@tiMainFont          =       21
    res@gsnLeftStringFontHeightF    =       0.02
    res@gsnRightStringFontHeightF   =       0.02
    res@gsnCenterStringFontHeightF  =       0.023
    res@gsnPolarLabelFontHeightF = .015
    res@mpGeophysicalLineThicknessF =2
    res@mpFillDrawOrder = "PostDraw"

    gsn_define_colormap(wks,colorbar)
    res@cnFillOn             = True                 ; turn on color
    res@cnLinesOn            = False        ; True is default
    res@cnLineLabelsOn       = False        ; True is default
    

    res@gsnSpreadColors      = True                 ; use full colormap
    res@mpLabelFontHeightF      =   0.03

    res@pmLabelBarWidthF        =   0.8 
    res@pmLabelBarHeightF       =   0.1 
    res@pmLabelBarOrthogonalPosF    =   0.15
    res@cnMaxLevelCount      = 100

    res@gsnPolar            =   "SH"
    res@mpMinLatF           =   latmin
    res@mpMaxLatF           =   latmax


   ; res@gsnPaperOrientation     = "Portrait"

    plot    =   gsn_csm_contour_map_polar(wks,dat,res)

    draw(plot)
end
    


    
