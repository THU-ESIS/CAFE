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
   	fi_data = "/mnt/CMIP5/cmip5_tmp/1010/year_data"
   end if
   if(.not.isvar("fi_name")) then
   	fi_name = "sic_OImon_FGOALS-g2_historicalGHG_r1i1p1_192701-199112_year.nc"
   end if
   if(.not.isvar("fig_data")) then
   	fig_data = "/mnt/CMIP5/cmip5_tmp/1010/fig_data"
   end if
   if(.not.isvar("fig_name")) then
   	fig_name = "sic_OImon_FGOALS-g2_historicalGHG_r1i1p1_192701-199112_TS_ANN_45_90_160_260"
   end if
   if(.not.isvar("txt_data")) then
   	txt_data = "/mnt/CMIP5/cmip5_tmp/1010/txt_data"
   end if
   if(.not.isvar("txt_name")) then
   	txt_name = "sic_OImon_FGOALS-g2_historicalGHG_r1i1p1_192701-199112_TS_ANN_45_90_160_260.txt"
   end if
   if(.not.isvar("var_name")) then
   	var_name="sic"
   end if
   if(.not.isvar("fig_type")) then
   	fig_type= "png"
   end if
   if(.not.isvar("start_year")) then
   	start_year=1927
   end if
   if(.not.isvar("end_year")) then
   	end_year=1991
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

   x_title     =   "year"
   ;linecolor   =   "red"
   ;linethick   =   2.0
   ;linedash    =   0
   xmin      =   start_year
   xmax      =   end_year
   nt        =   end_year-start_year+1

;************************************************
; process netCDF file
;************************************************

    fi      =   addfile(fi_data+"/"+fi_name,"r") 
    dat0     =   fi->$var_name$
    var0= dat0(time|:,lat|:,lon|:)
    dat = var0(:,{latmin:latmax},{lonmin:lonmax})

    time    =   cd_calendar(dat&time,1)/100
    ;print(time)

    var     = new((/2,nt/),double)
;计算区域平均，返回值是n-2维,此案例得到随时间变化的一维数组
    var_mean = wgt_areaave(dat,1.0,1.0,0) 
 printVarSummary(dat0)
 printVarSummary(var_mean)
printVarSummary(var)
    var(0,:)      = var_mean  
    
    
;计算一元线性回归 
    rc          =   regline(time,var_mean)    ;斜率(包含其他相关属性)
;print(rc)
    yave        =   dim_avg(var_mean)
    var_trend   =   rc*(time-rc@xave) + yave
    expression = "y="+tostring(rc)+"x"+"+"+tostring(yave)
    var(1,:)      = var_trend 

;************************************************
; create plot
;************************************************ 
    
    wks = gsn_open_wks(fig_type, fig_data+"/"+fig_name)
    res =   True

    ;res@gsnMaximize     =   True
    res@vpHeightF = 0.5        ; Changes the aspect ratio
    res@vpWidthF  = 0.75
    res@trXMinF   = xmin
    res@trXMaxF   = xmax
    ;xx  =  fspan(0,1.0,nt)
    ;xi  =  (/"1850","","1852","","1854","","1856","","1858","",\
    ;         "1860","","1862","","1864","","1866","","1868",""/)
    ;res@tmXBMode     = "Explicit"
    ;res@tmXBValues   = xx
    ;res@tmXBLabels   = xi

    res@xyMarkLineModes     = (/"MarkLines","Lines"/)            ; choose which have markers
    res@xyMarkers           = 16                     ; choose type of marker 
    res@xyMarkerColor       = "red"                  ; Marker color
    res@xyMarkerSizeF       = 0.005                  ; Marker size (default 0.01)
    res@xyLineThicknesses      = 2
    res@xyLineColors        = (/"black","blue"/)
    res@xyDashPatterns      = (/0,16/)
    
    res@tiMainString        =      "Annual Time Series of "+dat0@long_name+" ("+ start_year +" to "+end_year+")"
    res@tiXAxisString       =      x_title 
    res@tiYAxisString       =       dat0@long_name+" ("+dat0@units +" )"
    res@gsnLeftString       =       ""
    res@gsnRightString      =       ""
    res@gsnCenterString     =       expression
    res@tiMainFontHeightF   =       0.022
    res@tiXAxisFontHeightF  =       0.02
    res@tiYAxisFontHeightF  =       0.02
    ;res@gsnLeftStringFontHeightF    =       0.02
    ;res@gsnRightStringFontHeightF   =       0.02
    ;res@gsnCenterStringFontHeightF  =       0.02
    res@tmYLLabelFontHeightF    =       0.02
    res@tmXBLabelFontHeightF    =       0.02
    res@gsnPaperOrientation     = "portrait"
    
    res@pmLegendDisplayMode  = "Always"
    res@lgPerimOn            = False      ; No legend perimeter.
    res@pmLegendWidthF       =    0.15  ; change the width
    res@pmLegendHeightF      =    0.05  ; change the height
    res@xyExplicitLegendLabels = (/"var_mean","trend"/)

    res@pmLegendZone           = 0 
    res@pmLegendParallelPosF    = 0.35
    res@pmLegendOrthogonalPosF  = -0.42
    res@lgLabelFontHeightF=0.022

    plot    =   gsn_csm_xy(wks,time,var,res)

    header = (/"--------------------------------", \
            "year   value", \
            "--------------------------------"/)
    footer = (/"--------------------------------", \
            "Expression:"+expression, \
            "--------------------------------"/)
    alist = [/time,var_mean/]
    hlist = [/header/]
    flist = [/footer/]

    write_table(txt_data+"/"+txt_name, "w", hlist, "%s")
    write_table(txt_data+"/"+txt_name, "a", alist, "%4.0lf %f")
    write_table(txt_data+"/"+txt_name, "a", flist, "%s")

; hlist = [/header/]
; flist = [/footer/]
    delete(plot)
    delete(res)

end

    

