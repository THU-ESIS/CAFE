;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;;;;;;;;;;;;;;;;;;;Editor: Eric Xu;;;;;;;;;;;;;;;;;;;;;;;
;;;;;;;;;;;;;;;;;;;;;Date: 2015.04.06;;;;;;;;;;;;;;;;;;;;;;
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
   	fi_data = "/mnt/CMIP5/cmip5_tmp/1010/cdo_data"
   end if
   if(.not.isvar("fi_name")) then
   	fi_name = "sic_OImon_FGOALS-g2_historicalGHG_r1i1p1_192701-199112_cdo.nc"
   end if
   if(.not.isvar("fig_data")) then
   	fig_data = "/mnt/CMIP5/cmip5_tmp/1010/fig_data"
   end if
   if(.not.isvar("fig_name")) then
   	fig_name = "sic_OImon_FGOALS-g2_historicalGHG_r1i1p1_192701-199112_SEAS_CN_NH_45_90"
   end if
   if(.not.isvar("nc_data")) then
   	nc_data = "/mnt/CMIP5/cmip5_tmp/1010/nc_data"
   end if
   if(.not.isvar("nc_name")) then
   	nc_name = "sic_OImon_FGOALS-g2_historicalGHG_r1i1p1_192701-199112_SEAS_CN_NH_45_90.nc"
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
  	lonmin=0
   end if
   if(.not.isvar("lonmax")) then
   	lonmax=360
   end if
   
;************************************************
; process netCDF file
;************************************************
    
    fi      =   addfile(fi_data+"/"+fi_name,"r") 
    dat0     =   fi->$var_name$
    TIME   = fi->time
    YYYY   = cd_calendar(TIME,-1)/100 
    start_year=min(YYYY)
    end_year=max(YYYY)
    nt=(end_year-start_year)*12
    var0= dat0(time|:,lat|:,lon|:)                
    sic1 = var0(:,{latmin:latmax},{lonmin:lonmax})

  dim     =dimsizes(sic1)             
  
  sic1_1d = ndtooned(sic1) 
  sic1_4d = onedtond(sic1_1d,(/dim(0)/12,12,dim(1),dim(2)/))

  sic1_3d_winter = dim_avg_n(sic1_4d(:,(/0,1,11/),:,:),1)    ;DJF every year(12,1,2)
  sic1_3d_spring = dim_avg_n(sic1_4d(:,2:4,:,:),1)    ;MAM every year(3,4,5)
  sic1_3d_summer = dim_avg_n(sic1_4d(:,5:7,:,:),1)    ;JJA every year(6,7,8)
  sic1_3d_autumn = dim_avg_n(sic1_4d(:,8:10,:,:),1)   ;SON every year(9,10,11)

  sic1_2d  = new((/4,dim(1),dim(2)/),double) 
  sic1_2d(0,:,:) = dim_avg_n(sic1_3d_winter(:,:,:),0)    ;multiyear mean DJF  
  sic1_2d(1,:,:) = dim_avg_n(sic1_3d_spring(:,:,:),0)    ;multiyear mean MAM
  sic1_2d(2,:,:) = dim_avg_n(sic1_3d_summer(:,:,:),0)    ;multiyear mean JJA
  sic1_2d(3,:,:) = dim_avg_n(sic1_3d_autumn(:,:,:),0)    ;multiyear mean SON
  
  copy_VarMeta(sic1(0,:,:),sic1_2d(0,:,:))
  copy_VarMeta(sic1(0,:,:),sic1_2d(1,:,:))
  copy_VarMeta(sic1(0,:,:),sic1_2d(2,:,:))
  copy_VarMeta(sic1(0,:,:),sic1_2d(3,:,:))

;************************************************
; create plot
;************************************************ 
    
    wks = gsn_open_wks(fig_type, fig_data+"/"+fig_name)
    gsn_define_colormap(wks, "rainbow+gray")
    plot = new(4,graphic)

  res           = True
  res@gsnDraw              = False        ; don't draw yet
  res@gsnFrame             = False        ; don't advance frame yet
  res@gsnPolar             = "NH"

  res@mpFillOn             = True        ; turn off map fill
  res@mpMinLatF            = latmin         
  res@mpMaxLatF            = latmax
  res@mpLandFillColor      = 238
    
     
  res@tiYAxisString       =       ""
  res@gsnRightString      =       ""
  res@gsnLeftStringFontHeightF = .025           ; change font height of labels
  res@gsnPolarLabelFontHeightF = .025           ; change font height of labels

  res@cnFillOn             = True         ; turn on color fill 
  res@cnFillColors = (/0,70,75,80,85,90,95,100,105,110,115,120,125,130,170,175,180,185,190,195,200,205,210,215,220/)
;(/0,6,10,14,18,22,26,30,37,41,45,49,53,57,61,65,69,73,77,81,85,89,93,97,101/)
  res@cnFillDrawOrder      = "Predraw"    ; draw contours first

  ;res@cnLevelSelectionMode = "ManualLevels"      ; set manual contour levels
  ;res@cnMinLevelValF       = 6                 ; set min contour level
  ;res@cnMaxLevelValF       = 98                 ; set max contour level
  ;res@cnLevelSpacingF      =  4                ; set contour spacing
  res@cnMaxLevelCount      = 25
  res@cnLinesOn            = False        ; True is default
  res@cnLineLabelsOn       = False        ; True is default
  res@lbLabelBarOn         = False        ; turn off individual lb's

  resP                     =True
  resP@txString        = start_year +" to "+end_year+" Seasonal Cycle of "+dat0@long_name+" ("+tostring(latmin)+"N - "+tostring(latmax)+"N)"
  resP@gsnPanelLabelBar    = True         ; add common colorbar

  resP@lbLabelAutoStride   = True         ; auto stride on labels
  resP@lbPerimOn           = False
  resP@lbLabelFontHeightF  = 0.01
 ; resP@lbLabelStride       = 2        ; every 2th label

  resP@vpXF               = 0.15   
  resP@vpYF               = 0.10
  resP@vpWidthF           = 0.70                ; size
  resP@vpHeightF          = 0.05

  ;resP@lbTitleOn        =  True                ; turn on title
  ;resP@lbTitleString    = "%"                ; title string
  ;resP@lbTitlePosition  = "Right"              ; title position
  ;resP@lbTitleFontHeightF= .015                ; make title smaller

  do i=0,3
    if(i.eq.0)then
         res@gsnLeftString   =  "a)Winter"
    else if(i.eq.1)then
         res@gsnLeftString   =  "b)Spring"
    else if(i.eq.2)then
         res@gsnLeftString   =  "c)Summer"  
    else 
         res@gsnLeftString   =  "d)Autumn"
    end if
    end if
    end if
  plot(i)=gsn_csm_contour_map_polar(wks,sic1_2d(i,:,:),res)
  end do

  gsn_panel(wks,plot,(/2,2/),resP)      ; only plot 1st 4 
  print("complete")
  printVarSummary(sic1_2d)
;*******************************************
; export nc data
;*******************************************
  system("rm -f " + nc_data+"/"+nc_name)
  fout= addfile(nc_data+"/"+nc_name,"c")
  setfileoption(fout,"DefineMode",True)
  
  fout->lat    = (/sic1_2d&lat/)
  fout->lon    = (/sic1_2d&lon/)
  fout->sic1_2d     = (/sic1_2d/)
    delete(plot)
    delete(res)
    delete(resP) 
    

end

    
