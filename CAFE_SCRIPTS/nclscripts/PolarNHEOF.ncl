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
   	fi_data = "/mnt/CMIP5/cmip5_tmp/1010/ori_data"
   end if
   if(.not.isvar("fi_name")) then
   	fi_name = "sic_OImon_FGOALS-g2_historicalGHG_r1i1p1_192701-199112_ori.nc"
   end if
   if(.not.isvar("fig_data")) then
   	fig_data = "/mnt/CMIP5/cmip5_tmp/1010/fig_data"
   end if
   if(.not.isvar("fig_name")) then
   	fig_name = "sic_OImon_FGOALS-g2_historicalGHG_r1i1p1_192701-199112_EOF_NH_60_90"
   end if
   if(.not.isvar("nc_data")) then
   	nc_data = "/mnt/CMIP5/cmip5_tmp/1010/nc_data"
   end if
   if(.not.isvar("nc_name")) then
   	nc_name = "sic_OImon_FGOALS-g2_historicalGHG_r1i1p1_192701-199112_EOF_NH_60_90.nc"
   end if
   if(.not.isvar("txt_data")) then
   	txt_data = "/mnt/CMIP5/cmip5_tmp/1010/txt_data"
   end if
   if(.not.isvar("txt_name")) then
   	txt_name = "sic_OImon_FGOALS-g2_historicalGHG_r1i1p1_192701-199112_EOFTS_NH_60_90.txt"
   end if
   if(.not.isvar("var_name")) then
   	var_name="sic"
   end if
   if(.not.isvar("fig_type")) then
   	fig_type= "png"
   end if
   if(.not.isvar("latmin")) then
   	latmin=60
   end if
   if(.not.isvar("latmax")) then
   	latmax=90
   end if
;************************************************
; read in netCDF file and computation
;************************************************
   fi  =   addfile(fi_data+"/"+fi_name,"r")
   var =   fi->$var_name$
   lat=fi->lat
   lon=fi->lon
   
   TIME   = fi->time
   YYYYMM   = cd_calendar(TIME,-1)
   YYYY=YYYYMM/100
;************************************************
; dealing with data
;************************************************  
   ntime   = dimsizes(var&time)
   ny      = dimsizes(var&lat)
   nx      = dimsizes(var&lon)
   neof = 4
   var_reordered=var(time|:,lat|:,lon|:) 
   x0=var_reordered(:,{latmin:latmax},:)
   x0=dtrend_leftdim(x0,False)  ; 消除最左边一维的最小方差线性变化趋势的影响
   x1=x0(lat|:,lon|:,time|:)
   printVarSummary(x1)
   xClm      = clmMonLLT(x1)
   printVarSummary(xClm)
   x         = calcMonAnomLLT (x1, xClm)    ; replace with anonamlies
   x@long_name = "ANOMALIES: "+x@long_name
   printVarSummary(x)
   optEof = True
   optETS = False
;************************************************
; compute the EOF
;************************************************  
   eof    = eofunc_Wrap(x, neof, optEof)
   eof_ts = eofunc_ts_Wrap( x, eof,  optETS )
   eof_ts = dim_standardize_n( eof_ts, 0, 1) ;标准化

;************************************************
; creating plot
;************************************************
  wks = gsn_open_wks(fig_type, fig_data+"/"+fig_name) 
  gsn_define_colormap(wks,"BlueRed")
  plot = new(neof,graphic)
   
  res                      = True         
  res@gsnDraw              = False        ; don't draw yet
  res@gsnFrame             = False        ; don't advance frame yet
  res@gsnPolar             = "NH"
  res@gsnAddCyclic         = False

  res@gsnSpreadColors      = True         ; spread out color table

  res@mpFillOn             = False        ; turn off map fill
  res@mpMinLatF            = latmin         ; zoom in on map
  res@mpMaxLatF            = latmax
  res@mpGeophysicalLineThicknessF =2
  res@gsnLeftStringFontHeightF =0.022
  res@gsnRightStringFontHeightF =0.022
  res@gsnPolarLabelFontHeightF = .02


  res@cnFillOn             = True         ; turn on color fill
  res@cnLinesOn            = False        ; True is default
  res@cnLineLabelsOn       = False        ; True is default
  res@lbLabelBarOn         = False        ; turn off individual lb's
  
  symMinMaxPlt(eof, 16, False, res)
  ; panel plot only resources
  resP                     = True         ; modify the panel plot
  resP@gsnMaximize         = True         ; large format
  resP@gsnPanelLabelBar    = True         ; add common colorbar
  resP@lbLabelAutoStride   = True         ; auto stride on labels
  resP@txString             = "EOF of "+var@long_name+": "+tostring(min(YYYY))+"-"+tostring(max(YYYY))+ " ("+tostring(latmin)+"N - "+tostring(latmax)+"N)"

;*******************************************
; Plot standard patterns
;*******************************************
 
     do n=0,neof-1
     res@gsnLeftString  = "EOF "+(n+1)
     res@gsnRightString = sprintf("%5.1f", eof@pcvar(n)) +"%"
     plot(n)=gsn_csm_contour_map_polar(wks,eof(n,:,:),res)
     end do
     gsn_panel(wks,plot(0:3),(/2,2/),resP)     ; only plot the 1st four

;*******************************************
; time series (principal component) plot
;*******************************************
  eof_ts@long_name = "Standardized"

  rts           = True
  rts@gsnDraw   = False       ; don't draw yet
  rts@gsnFrame  = False       ; don't advance frame yet
  rts@gsnScale  = True        ; force text scaling
  rts@gsnMaximize         = True 

  rts@vpHeightF = 0.40        ; Changes the aspect ratio
  rts@vpWidthF  = 0.90
  rts@vpXF      = 0.05        ; change start locations
  rts@vpYF      = 0.75        ; the plot

; these four resources allow the user to stretch the plot size, and
; decide exactly where on the page to draw it.
  rts@gsnYRefLine           = 0.              ; reference line   
 ; rts@gsnXYBarChart         = True            ; create bar chart 
  rts@gsnAboveYRefLineColor = "red"           ; above ref line fill red
  rts@gsnBelowYRefLineColor = "blue"          ; below ref line fill blue
  rts@gsnXYBarChartOutlineThicknessF  = 0.

  rts@tmXTOn  = False
  rts@tmYROn  = False
  year   = yyyymm_to_yyyyfrac(YYYYMM, 0.0); 
  rts@trXMinF = min(year)
  ;print(year)
  rts@trXMaxF = max(year)

; panel plot only resources
  rtsP                     = True             ; modify the panel plot
  rtsP@gsnMaximize         = True             ; large format

  rtsP@txString            = "Time Series of "+var@long_name+": "+tostring(min(YYYY))+"-"+tostring(max(YYYY))+ " ("+tostring(latmin)+"N - "+tostring(latmax)+"N)"

  rtsP@gsnPanelFigureStrings = (/"a)","b)","c)","d)"/)
  rtsP@gsnPanelFigureStringsPerimOn  = False
  rtsP@amJust     = "BottomRight"
  rtsP@gsnPanelFigureStringsFontHeightF  = 0.01

  do n=0,neof-1
     rts@gsnLeftString  = "EOF "+(n+1)
     rts@gsnRightString = sprintf("%5.1f",eof@pcvar(n)) +"%"
     plot(n) = gsn_csm_xy (wks,year,eof_ts(n,:),rts)
  end do
  gsn_panel(wks,plot(0:3),(/2,2/),rtsP)        ; draw all 'neof' as one plot
;*******************************************
; export EOF nc data
;*******************************************
  system("rm -f " + nc_data+"/"+nc_name)
  fout= addfile(nc_data+"/"+nc_name,"c")
  setfileoption(fout,"DefineMode",True)
  
  fAtt               = True            ; assign file attributes
  fAtt@title         = "EOF of "+var@long_name+": "+tostring(min(YYYY))+"-"+tostring(max(YYYY)) 
  fAtt@source_file   = fi_name
  fAtt@Conventions   = "None"   
  fAtt@creation_date = systemfunc ("date")        
  fileattdef( fout, fAtt )            ; copy file attributes  
  evn=eof&evn
  nlon=dimsizes(eof&lon)
  nlat=dimsizes(eof&lat)
  print(evn)
  dimNames = (/"evn", "lat", "lon"/)  
  dimSizes = (/neof,  nlat,  nlon/)
  dimUnlim = (/False, False, False/)
  print(dimSizes)
  filedimdef(fout,dimNames,dimSizes,dimUnlim)
  printVarSummary(eof)

  filevardef(fout, "evn"  ,typeof(evn),getvardims(evn))
  filevardef(fout, "lat"  ,typeof(lat),getvardims(eof&lat))
  filevardef(fout, "lon"  ,typeof(eof&lon),getvardims(eof&lon))
  filevardef(fout, "eof"  ,typeof(eof)  ,getvardims(eof))

  filevarattdef(fout,"lat"  ,eof&lat)
  filevarattdef(fout,"lon"  ,eof&lon)
  filevarattdef(fout,"eof"  ,eof)
  filevarattdef(fout,"evn"  ,evn)

  setfileoption(fout,"DefineMode",False)

  fout->lat    = (/eof&lat/)
  fout->lon    = (/eof&lon/)
  fout->eof      = (/eof/)
;*******************************************
; export time series txt data
;******************************************* 
  header = (/"-----------------------------------------", \
               "time    EOF1    EOF2    EOF3    EOF4", \
               "-----------------------------------------"/)
    footer = (/"------------------------------------------------", \
            "EOF1:"+sprintf("%5.1f", eof@pcvar(0)) +"%"+"  EOF2:"+sprintf("%5.1f", eof@pcvar(1)) +"%"+"  EOF3:"+sprintf("%5.1f", eof@pcvar(2)) +"%"+"  EOF4:"+sprintf("%5.1f", eof@pcvar(3)) +"%", \
            "------------------------------------------------"/)
    alist = [/YYYYMM,eof_ts(0,:),eof_ts(1,:),eof_ts(2,:),eof_ts(3,:)/]
    hlist = [/header/]
    flist = [/footer/]

    write_table(txt_data+"/"+txt_name, "w", hlist, "%s")
    write_table(txt_data+"/"+txt_name, "a", alist, "%d %8.3f %8.3f %8.3f %8.3f")
    write_table(txt_data+"/"+txt_name, "a", flist, "%s")
end
