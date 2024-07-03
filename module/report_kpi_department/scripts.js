import {
  IsEmpty, resetInputExceptChoice, sendViaFetchForm, AlertElemBS5, ConfirmElemBS5,
  parseVersion, compareVersions, childVersion
} from '../../../third-party/utility-yudhi/utils.js';

$.fn.dataTable.ext.errMode = 'none';

const alertComponent = new AlertElemBS5('alertComponent1');
const confirmComponent = new ConfirmElemBS5('confirmComponent1');
const dgUtamaYearInput = document.getElementById('dgUtamaYearInput');
const dgUtamaCompanyInput = document.getElementById('dgUtamaCompanyInput');
const dgUtamaDepartmentInput = document.getElementById('dgUtamaDepartmentInput');
const dgUtamaYearBtn = document.getElementById('dgUtamaYearBtn');
const dgUtamaUserEntry = document.getElementById('dgUtamaUserEntry');
const dgUtamaLastUpdate = document.getElementById('dgUtamaLastUpdate');
const dgUtamaYear1 = document.getElementById('dgUtamaYear1');
const btnLaporan = document.getElementById('btnLaporan');
const dgLaporan = document.getElementById('dgLaporan');
const trUpperLaporan = document.getElementById('trUpperLaporan');
const trBottomLaporan = document.getElementById('trBottomLaporan');