#!/usr/bin/env python3
"""
HL7 v2.5.1 Lab Machine Simulator - BioAnalyzer 3000
Simulates a medical laboratory analyzer with HTTP API and TCP socket transport.
"""

import json
import math
import random
import socket
import socketserver
import struct
import sys
import threading
import time
import uuid
from datetime import datetime, timedelta

from flask import Flask, jsonify, request

# =============================================================================
# EXAM DATABASE
# =============================================================================

CODE_ALIASES = {
    "CBC": "NFS", "NFS Complete": "NFS", "Hématologie": "NFS", "Bilan Sanguin": "NFS",
    "Glycémie": "GLYC", "Glyc": "GLYC", "Glyco": "GLYC",
    "HbA1c": "HB1AC", "HBA1C": "HB1AC", "A1C": "HB1AC", "Hémoglobine Glyquée": "HB1AC",
    "BUN": "UREE", "Azote": "UREE", "Urée": "UREE",
    "Créat": "CREAT", "Créatinine": "CREAT", "DFG": "CREAT",
    "CRP Qnt": "CRP", "Protéine C": "CRP", "Protéine C Réactive": "CRP",
    "VS 1h": "VS", "VS": "VS", "Sédimentation": "VS",
    "Ionogramme": "IONO", "Electrolytes": "IONO", "Na K Cl": "IONO",
    "TSH Ultra": "TSH", "Thyroide": "TSH", "Thyroïde": "TSH", "TSHus": "TSH",
    "Uroculture": "ECBU", "ECB": "ECBU", "Urine": "ECBU",
    "LIPID": "GLYC", "Lipides": "GLYC",
    "ACUR": "CREAT", "Urique": "CREAT",
    "AMY": "CREAT", "Amylase": "CREAT",
    "BIL": "GLYC", "Bilirubine": "GLYC",
    "TRANS": "GLYC", "Transaminases": "GLYC", "ASAT": "GLYC", "ALAT": "GLYC",
    "HEMOC": "CRP", "Hémoculture": "CRP",
    "CALCI": "IONO", "Calcium": "IONO",
    "FER": "CRP", "Ferritine": "CRP",
    "VITD": "GLYC", "Vitamine D": "GLYC",
    "PSA": "GLYC", "Prostate": "GLYC",
    "PROT": "GLYC", "Protéines": "GLYC",
    "VITB12": "CRP", "Vitamine B12": "CRP",
    "CLER": "CREAT", "Clairance": "CREAT",
    "ASLO": "CRP", "Streptolysine": "CRP",
    "TPINR": "VS", "TP": "VS", "INR": "VS",
    "TCA": "VS", "Coagulation": "VS",
    "STREP": "CRP", "Streptocoque": "CRP",
    "COPRO": "ECBU", "Coproculture": "ECBU",
    "FACTR": "CRP", "Rhumatoïde": "CRP",
    "LIPAS": "CREAT", "Lipase": "CREAT",
}


def _resolve_fallback(code):
    c = code.upper().strip()
    if any(w in c for w in ["SANG", "BLOOD", "HEMO", "HEMOGLOB", "PLAQ", "WBC", "RBC", "HEM"]):
        return "NFS"
    if any(w in c for w in ["URINE", "URINARY", "URIN"]):
        return "ECBU"
    if any(w in c for w in ["THYROID", "THYRO"]):
        return "TSH"
    if any(w in c for w in ["COAG", "INR", "PROTHROMB"]):
        return "VS"
    if any(w in c for w in ["FERR", "IRON"]):
        return "CRP"
    return random.choice(["NFS", "GLYC", "CRP", "IONO", "UREE", "CREAT"])


EXAM_DATABASE = {
    "NFS": {
        "code": "NFS",
        "name": "Numération Formule Sanguine",
        "category": "hematology",
        "parameters": [
            {
                "code": "HGB",
                "name": "Hémoglobine",
                "unit": "g/dL",
                "reference_ranges": {"M": (13.0, 17.0), "F": (12.0, 16.0)},
                "mean": 14.5,
                "std": 1.2,
                "type": "numeric",
            },
            {
                "code": "HCT",
                "name": "Hématocrite",
                "unit": "%",
                "reference_ranges": {"M": (40.0, 54.0), "F": (36.0, 46.0)},
                "mean": 45.0,
                "std": 3.0,
                "type": "numeric",
            },
            {
                "code": "WBC",
                "name": "Leucocytes",
                "unit": "G/L",
                "reference_ranges": {"all": (4.0, 10.0)},
                "mean": 7.0,
                "std": 1.5,
                "type": "numeric",
            },
            {
                "code": "PLT",
                "name": "Plaquettes",
                "unit": "G/L",
                "reference_ranges": {"all": (150.0, 400.0)},
                "mean": 250.0,
                "std": 50.0,
                "type": "numeric",
            },
            {
                "code": "NEUT",
                "name": "Neutrophiles",
                "unit": "%",
                "reference_ranges": {"all": (40.0, 75.0)},
                "mean": 55.0,
                "std": 8.0,
                "type": "numeric",
            },
            {
                "code": "LYMPH",
                "name": "Lymphocytes",
                "unit": "%",
                "reference_ranges": {"all": (20.0, 45.0)},
                "mean": 30.0,
                "std": 5.0,
                "type": "numeric",
            },
            {
                "code": "MONO",
                "name": "Monocytes",
                "unit": "%",
                "reference_ranges": {"all": (2.0, 10.0)},
                "mean": 5.0,
                "std": 1.5,
                "type": "numeric",
            },
            {
                "code": "EOS",
                "name": "Éosinophiles",
                "unit": "%",
                "reference_ranges": {"all": (1.0, 6.0)},
                "mean": 3.0,
                "std": 1.0,
                "type": "numeric",
            },
            {
                "code": "BASO",
                "name": "Basophiles",
                "unit": "%",
                "reference_ranges": {"all": (0.0, 1.0)},
                "mean": 0.3,
                "std": 0.2,
                "type": "numeric",
            },
            {
                "code": "MCV",
                "name": "VGM",
                "unit": "fL",
                "reference_ranges": {"all": (80.0, 100.0)},
                "mean": 90.0,
                "std": 5.0,
                "type": "numeric",
            },
            {
                "code": "MCH",
                "name": "TCMH",
                "unit": "pg",
                "reference_ranges": {"all": (27.0, 33.0)},
                "mean": 30.0,
                "std": 1.5,
                "type": "numeric",
            },
            {
                "code": "MCHC",
                "name": "CCP",
                "unit": "g/dL",
                "reference_ranges": {"all": (32.0, 36.0)},
                "mean": 34.0,
                "std": 1.0,
                "type": "numeric",
            },
        ],
    },
    "GLYC": {
        "code": "GLYC",
        "name": "Glycémie à jeun",
        "category": "biochemistry",
        "parameters": [
            {
                "code": "GLU",
                "name": "Glycémie",
                "unit": "g/L",
                "reference_ranges": {"all": (0.70, 1.10)},
                "mean": 0.90,
                "std": 0.10,
                "type": "numeric",
            },
        ],
    },
    "HB1AC": {
        "code": "HB1AC",
        "name": "Hémoglobine Glyquée",
        "category": "biochemistry",
        "parameters": [
            {
                "code": "HBA1C",
                "name": "HbA1c",
                "unit": "%",
                "reference_ranges": {"all": (4.0, 5.7)},
                "mean": 5.2,
                "std": 0.4,
                "type": "numeric",
            },
            {
                "code": "EGM",
                "name": "Estimation moyenne glycémique",
                "unit": "g/L",
                "reference_ranges": {"all": (0.70, 1.30)},
                "mean": 1.05,
                "std": 0.15,
                "type": "numeric",
                "derived_from": "HBA1C",
                "formula": lambda hba1c: 1.59 * hba1c - 2.59,
            },
        ],
    },
    "UREE": {
        "code": "UREE",
        "name": "Urée sanguine",
        "category": "biochemistry",
        "parameters": [
            {
                "code": "UREA",
                "name": "Urée",
                "unit": "g/L",
                "reference_ranges": {"all": (0.15, 0.45)},
                "mean": 0.30,
                "std": 0.07,
                "type": "numeric",
            },
            {
                "code": "BUN",
                "name": "Azote uréique",
                "unit": "g/L",
                "reference_ranges": {"all": (0.10, 0.30)},
                "mean": 0.18,
                "std": 0.04,
                "type": "numeric",
                "derived_from": "UREA",
                "formula": lambda urea: urea * 0.467,
            },
        ],
    },
    "CREAT": {
        "code": "CREAT",
        "name": "Créatinine sanguine",
        "category": "biochemistry",
        "parameters": [
            {
                "code": "CREAT",
                "name": "Créatinine",
                "unit": "mg/L",
                "reference_ranges": {"M": (7.0, 13.0), "F": (6.0, 11.0)},
                "mean": 9.0,
                "std": 1.5,
                "type": "numeric",
            },
            {
                "code": "DFG",
                "name": "Débit de filtration glomérulaire",
                "unit": "mL/min",
                "reference_ranges": {"all": (90.0, 150.0)},
                "mean": 110.0,
                "std": 15.0,
                "type": "numeric",
                "derived_from": "CREAT",
                "formula": lambda creat: max(10, 120 - (creat - 9) * 8),
            },
            {
                "code": "CRCL",
                "name": "Créatinine Clearance",
                "unit": "mL/min",
                "reference_ranges": {"all": (90.0, 140.0)},
                "mean": 115.0,
                "std": 12.0,
                "type": "numeric",
                "derived_from": "CREAT",
                "formula": lambda creat: max(30, 125 - (creat - 9) * 7),
            },
        ],
    },
    "CRP": {
        "code": "CRP",
        "name": "Protéine C Réactive",
        "category": "immunology",
        "parameters": [
            {
                "code": "CRP",
                "name": "CRP",
                "unit": "mg/L",
                "reference_ranges": {"all": (0.0, 6.0)},
                "mean": 2.0,
                "std": 1.5,
                "type": "numeric",
            },
            {
                "code": "VS",
                "name": "VS",
                "unit": "mm/h",
                "reference_ranges": {"M": (0.0, 15.0), "F": (0.0, 20.0)},
                "mean": 8.0,
                "std": 4.0,
                "type": "numeric",
            },
        ],
    },
    "VS": {
        "code": "VS",
        "name": "Vitesse de Sédimentation",
        "category": "hematology",
        "parameters": [
            {
                "code": "VS1",
                "name": "VS 1ère heure",
                "unit": "mm/h",
                "reference_ranges": {"M": (0.0, 15.0), "F": (0.0, 20.0)},
                "mean": 8.0,
                "std": 4.0,
                "type": "numeric",
            },
            {
                "code": "VS2",
                "name": "VS 2ème heure",
                "unit": "mm/h",
                "reference_ranges": {"M": (0.0, 30.0), "F": (0.0, 40.0)},
                "mean": 15.0,
                "std": 7.0,
                "type": "numeric",
            },
        ],
    },
    "IONO": {
        "code": "IONO",
        "name": "Ionogramme sanguin",
        "category": "biochemistry",
        "parameters": [
            {
                "code": "NA",
                "name": "Sodium",
                "unit": "mmol/L",
                "reference_ranges": {"all": (135.0, 145.0)},
                "mean": 140.0,
                "std": 2.5,
                "type": "numeric",
            },
            {
                "code": "K",
                "name": "Potassium",
                "unit": "mmol/L",
                "reference_ranges": {"all": (3.5, 5.0)},
                "mean": 4.2,
                "std": 0.35,
                "type": "numeric",
            },
            {
                "code": "CL",
                "name": "Chlorure",
                "unit": "mmol/L",
                "reference_ranges": {"all": (96.0, 106.0)},
                "mean": 101.0,
                "std": 2.5,
                "type": "numeric",
            },
            {
                "code": "CA",
                "name": "Calcium",
                "unit": "mmol/L",
                "reference_ranges": {"all": (2.20, 2.60)},
                "mean": 2.40,
                "std": 0.10,
                "type": "numeric",
            },
            {
                "code": "MG",
                "name": "Magnésium",
                "unit": "mmol/L",
                "reference_ranges": {"all": (0.70, 1.00)},
                "mean": 0.85,
                "std": 0.07,
                "type": "numeric",
            },
            {
                "code": "HCO3",
                "name": "Bicarbonate",
                "unit": "mmol/L",
                "reference_ranges": {"all": (22.0, 29.0)},
                "mean": 25.0,
                "std": 1.8,
                "type": "numeric",
            },
            {
                "code": "PHOS",
                "name": "Phosphore",
                "unit": "mmol/L",
                "reference_ranges": {"all": (0.80, 1.50)},
                "mean": 1.10,
                "std": 0.15,
                "type": "numeric",
            },
        ],
    },
    "TSH": {
        "code": "TSH",
        "name": "Hormone Thyréostimulante",
        "category": "biochemistry",
        "parameters": [
            {
                "code": "TSH",
                "name": "TSH",
                "unit": "mUI/L",
                "reference_ranges": {"all": (0.4, 4.0)},
                "mean": 2.0,
                "std": 0.8,
                "type": "numeric",
            },
            {
                "code": "FT4",
                "name": "T4L",
                "unit": "pmol/L",
                "reference_ranges": {"all": (12.0, 22.0)},
                "mean": 16.5,
                "std": 2.5,
                "type": "numeric",
            },
            {
                "code": "FT3",
                "name": "T3L",
                "unit": "pmol/L",
                "reference_ranges": {"all": (3.1, 6.8)},
                "mean": 4.8,
                "std": 0.8,
                "type": "numeric",
            },
        ],
    },
    "ECBU": {
        "code": "ECBU",
        "name": "Examen Cytobactériologique des Urines",
        "category": "urinalysis",
        "parameters": [
            {
                "code": "PH",
                "name": "pH",
                "unit": "",
                "reference_ranges": {"all": (5.0, 8.0)},
                "mean": 6.0,
                "std": 0.7,
                "type": "numeric",
            },
            {
                "code": "SG",
                "name": "Densité",
                "unit": "",
                "reference_ranges": {"all": (1.005, 1.030)},
                "mean": 1.015,
                "std": 0.005,
                "type": "numeric",
            },
            {
                "code": "PRO",
                "name": "Protéines",
                "unit": "g/L",
                "reference_ranges": {"all": (0.0, 0.15)},
                "mean": 0.05,
                "std": 0.03,
                "type": "numeric",
                "qualitative": {"low": "Négatif", "normal": "Négatif", "high": "Positif"},
            },
            {
                "code": "GLU_U",
                "name": "Glucose",
                "unit": "g/L",
                "reference_ranges": {"all": (0.0, 0.0)},
                "mean": 0.0,
                "std": 0.0,
                "type": "numeric",
                "qualitative": {"low": "Négatif", "normal": "Négatif", "high": "Positif"},
            },
            {
                "code": "LEU",
                "name": "Leucocytes",
                "unit": "/µL",
                "reference_ranges": {"all": (0.0, 25.0)},
                "mean": 8.0,
                "std": 5.0,
                "type": "numeric",
            },
            {
                "code": "ERY_U",
                "name": "Érythrocytes",
                "unit": "/µL",
                "reference_ranges": {"all": (0.0, 10.0)},
                "mean": 3.0,
                "std": 2.0,
                "type": "numeric",
            },
            {
                "code": "NIT",
                "name": "Nitrites",
                "unit": "",
                "reference_ranges": {"all": (0, 0)},
                "mean": 0,
                "std": 0,
                "type": "qualitative",
                "qualitative_values": ["Négatif", "Positif"],
                "abnormal_chance": 0.05,
            },
            {
                "code": "RBC_U",
                "name": "Hématies",
                "unit": "/µL",
                "reference_ranges": {"all": (0.0, 5.0)},
                "mean": 2.0,
                "std": 1.0,
                "type": "numeric",
            },
            {
                "code": "EPI",
                "name": "Cellules épithéliales",
                "unit": "/µL",
                "reference_ranges": {"all": (0.0, 10.0)},
                "mean": 3.0,
                "std": 2.0,
                "type": "numeric",
            },
            {
                "code": "BACT",
                "name": "Bactéries",
                "unit": "",
                "reference_ranges": {"all": (0, 0)},
                "mean": 0,
                "std": 0,
                "type": "qualitative",
                "qualitative_values": ["Absentes", "Présentes"],
                "abnormal_chance": 0.05,
            },
            {
                "code": "YEST",
                "name": "Levures",
                "unit": "",
                "reference_ranges": {"all": (0, 0)},
                "mean": 0,
                "std": 0,
                "type": "qualitative",
                "qualitative_values": ["Absentes", "Présentes"],
                "abnormal_chance": 0.03,
            },
        ],
    },
}


# =============================================================================
# RESULT GENERATION
# =============================================================================


def get_reference_range(param, sex="M"):
    ref = param["reference_ranges"]
    if sex in ref:
        return ref[sex]
    return ref.get("all", (0, 0))


def generate_numeric_value(param, sex="M", abnormal=False):
    low, high = get_reference_range(param, sex)
    mean = param["mean"]
    std = param["std"]

    if param["code"] in ("GLU_U",):
        low = 0.0
        high = 0.0

    if std == 0 and low == high == 0:
        return 0.0

    if abnormal:
        direction = random.choice(["low", "high"])
        if direction == "low":
            val = random.gauss(low - std * 1.5, std * 0.5)
            val = min(val, low - 0.001)
        else:
            val = random.gauss(high + std * 1.5, std * 0.5)
            val = max(val, high + 0.001)
    else:
        val = random.gauss(mean, std)
        val = max(low, min(high, val))

    if std < 0.01:
        val = round(val, 3)
    elif std < 1.0:
        val = round(val, 2)
    elif std < 10.0:
        val = round(val, 1)
    else:
        val = round(val, 0)

    return val


def generate_results(exam_code, sex="M"):
    exam = EXAM_DATABASE.get(exam_code)
    if not exam:
        return None

    roll = random.random()
    if roll < 0.85:
        abnormal_count = 0
    elif roll < 0.95:
        abnormal_count = random.choice([1, 2])
    else:
        abnormal_count = random.randint(2, min(4, len(exam["parameters"])))

    abnormal_params = set()
    if abnormal_count > 0:
        param_indices = list(range(len(exam["parameters"])))
        random.shuffle(param_indices)
        abnormal_params = set(param_indices[:abnormal_count])

    results = []
    generated_values = {}

    for i, param in enumerate(exam["parameters"]):
        is_abnormal = i in abnormal_params

        if param["type"] == "qualitative" and "qualitative_values" in param:
            abnormal_chance = param.get("abnormal_chance", 0.15)
            if is_abnormal or random.random() < abnormal_chance:
                value_str = param["qualitative_values"][1]
                status = "high"
            else:
                value_str = param["qualitative_values"][0]
                status = "normal"
            low, high = get_reference_range(param, sex)
            results.append(
                {
                    "parameter_code": param["code"],
                    "parameter": param["name"],
                    "value": value_str,
                    "unit": param["unit"],
                    "reference_range": f"{param['name']} Normal",
                    "status": status,
                }
            )
            generated_values[param["code"]] = None
            continue

        if "derived_from" in param and param["derived_from"] in generated_values:
            dep_val = generated_values[param["derived_from"]]
            if dep_val is not None:
                value = param["formula"](dep_val)
                low, high = get_reference_range(param, sex)
                if std := param["std"]:
                    if std < 0.01:
                        value = round(value, 3)
                    elif std < 1.0:
                        value = round(value, 2)
                    elif std < 10.0:
                        value = round(value, 1)
                    else:
                        value = round(value, 0)
            else:
                value = generate_numeric_value(param, sex, abnormal=is_abnormal)
        else:
            value = generate_numeric_value(param, sex, abnormal=is_abnormal)

        low, high = get_reference_range(param, sex)
        if value < low:
            status = "low"
        elif value > high:
            status = "high"
        else:
            status = "normal"

        generated_values[param["code"]] = value

        if param["code"] in ("GLU_U",) and value == 0.0:
            ref_str = "Négatif"
        elif param["code"] in ("NIT",):
            ref_str = "Négatif"
        else:
            ref_str = f"{low} - {high}"

        if param["unit"]:
            value_str = f"{value}"
        else:
            value_str = f"{value}"

        qualitative = param.get("qualitative", {})
        if status in qualitative:
            value_str = qualitative[status]

        results.append(
            {
                "parameter_code": param["code"],
                "parameter": param["name"],
                "value": value_str,
                "unit": param["unit"],
                "reference_range": ref_str,
                "status": status,
            }
        )

    return results


# =============================================================================
# HL7 MESSAGE GENERATION
# =============================================================================


def generate_hl7_timestamp():
    return datetime.now().strftime("%Y%m%d%H%M%S")


def generate_message_id():
    return f"MSG{uuid.uuid4().hex[:8].upper()}"


def generate_ack(original_msg_id):
    ts = generate_hl7_timestamp()
    msg_id = generate_message_id()
    lines = [
        f"MSH|^~\\&|ANALYZER|MEDIX_LAB|LIS|MEDIX|{ts}||ACK|{msg_id}|P|2.5.1",
        f"MSA|AA|{original_msg_id}|Order received and processed",
    ]
    return "\r".join(lines)


def generate_oru_response(order_data, results):
    ts = generate_hl7_timestamp()
    msg_id = generate_message_id()
    patient = order_data.get("patient", {})
    order_id = order_data.get("order_id", "UNKNOWN")
    exam_code = order_data.get("exam_code", "UNKNOWN")

    exam = EXAM_DATABASE.get(exam_code, {})
    exam_name = exam.get("name", "Unknown Exam")

    patient_id = patient.get("id", "000000")
    patient_name = patient.get("name", "UNKNOWN^PATIENT")
    birth_date = patient.get("birth_date", "19000101").replace("-", "")
    sex = patient.get("sex", "M")

    name_parts = patient_name.split(" ", 1)
    family_name = name_parts[0].upper() if name_parts else "UNKNOWN"
    given_name = name_parts[1].upper() if len(name_parts) > 1 else "PATIENT"

    lines = [
        f"MSH|^~\\&|ANALYZER|MEDIX_LAB|LIS|MEDIX|{ts}||ORU^R01|{msg_id}|P|2.5.1",
        f"PID|1||{patient_id}^MEDIX_LAB||{family_name}^{given_name}||{birth_date}|{sex}",
        f"ORC|RE|{order_id}|||CM",
        f"OBR|1||{order_id}|{exam_code}^{exam_name}|||{ts}|||||||{ts}|||ANALYZER|||",
    ]

    for idx, result in enumerate(results, start=1):
        code = result["parameter_code"]
        name = result["parameter"]
        value = result["value"]
        unit = result["unit"]
        ref = result["reference_range"]
        status = result["status"]
        flag = ""
        if status == "high":
            flag = "H"
        elif status == "low":
            flag = "L"

        obx = f"OBX|{idx}|NM|{code}^{name}||{value}|{unit}|{ref}|||{flag}|F"
        lines.append(obx)

    return "\r".join(lines)


# =============================================================================
# MLLP TCP SERVER
# =============================================================================

MLLP_START = b"\x0b"
MLLP_END = b"\x1c\x0d"


class MLLPHandler(socketserver.BaseRequestHandler):
    def handle(self):
        try:
            data = self._receive_mllp()
            if not data:
                return

            hl7_message = data.decode("ascii", errors="replace")
            print(
                f"  [TCP] Message received from {self.client_address[0]}:{self.client_address[1]}"
            )

            parsed = self._parse_hl7(hl7_message)

            exam_code_raw = parsed.get("exam_code", "GLYC")
            exam_code = exam_code_raw.upper().strip()

            if exam_code not in EXAM_DATABASE:
                alias_upper = {k.upper(): v for k, v in CODE_ALIASES.items()}
                if exam_code in alias_upper:
                    resolved = alias_upper[exam_code]
                    if resolved in EXAM_DATABASE:
                        exam_code = resolved
                        print(f"  [TCP ALIAS] Mapped {exam_code_raw} -> {exam_code}")
                else:
                    code_map = {k.upper(): k for k in EXAM_DATABASE.keys()}
                    if exam_code in code_map:
                        exam_code = code_map[exam_code]
                    else:
                        exam_code = _resolve_fallback(exam_code_raw)
                        print(f"  [TCP FALLBACK] {exam_code_raw} -> {exam_code}")

            if exam_code not in EXAM_DATABASE:
                print(f"  [TCP] Unknown exam code: {exam_code_raw}, defaulting to NFS")
                exam_code = "NFS"

            order_data = {
                "order_id": parsed.get("order_id", f"TCP-{uuid.uuid4().hex[:6].upper()}"),
                "exam_code": exam_code,
                "patient": {
                    "id": parsed.get("patient_id", "0"),
                    "name": parsed.get("patient_name", "UNKNOWN^PATIENT"),
                    "birth_date": parsed.get("birth_date", "19000101"),
                    "sex": parsed.get("sex", "M"),
                },
            }

            print(f"  [TCP] Generating results for exam: {exam_code}")
            delay = random.uniform(1.5, 3.5)
            time.sleep(delay)

            results = generate_results(exam_code, order_data["patient"]["sex"])
            if results is None:
                print(f"  [TCP] Failed to generate results for: {exam_code}")
                return

            oru_msg = generate_oru_response(order_data, results)
            self._send_mllp(oru_msg.encode("ascii"))
            print(f"  [TCP] ORU response sent ({len(results)} results)")

        except Exception as e:
            print(f"  [TCP] Error: {e}")

    def _receive_mllp(self):
        buffer = b""
        started = False
        while True:
            chunk = self.request.recv(4096)
            if not chunk:
                return None
            buffer += chunk
            if not started:
                idx = buffer.find(MLLP_START)
                if idx >= 0:
                    started = True
                    buffer = buffer[idx + 1 :]
            if started:
                end_idx = buffer.find(MLLP_END)
                if end_idx >= 0:
                    return buffer[:end_idx]
        return None

    def _send_mllp(self, data):
        self.request.sendall(MLLP_START + data + MLLP_END)

    def _parse_hl7(self, message):
        result = {}
        segments = message.split("\r")
        for segment in segments:
            fields = segment.split("|")
            seg_type = fields[0].strip()
            if seg_type == "MSH":
                if len(fields) > 8:
                    result["message_type"] = fields[8]
                if len(fields) > 9:
                    result["message_id"] = fields[9]
            elif seg_type == "PID":
                if len(fields) > 3:
                    pid3 = fields[3].split("^")
                    result["patient_id"] = pid3[0] if pid3 else ""
                if len(fields) > 5:
                    pid5 = fields[5].split("^")
                    if len(pid5) >= 2:
                        result["patient_name"] = f"{pid5[0]} {pid5[1]}"
                    elif pid5:
                        result["patient_name"] = pid5[0]
                if len(fields) > 7:
                    result["birth_date"] = fields[7]
                if len(fields) > 8:
                    result["sex"] = fields[8]
            elif seg_type == "ORC":
                if len(fields) > 2:
                    result["order_id"] = fields[2]
            elif seg_type == "OBR":
                if len(fields) > 4:
                    obr4 = fields[4].split("^")
                    result["exam_code"] = obr4[0] if obr4 else ""
        return result


class ThreadedTCPServer(socketserver.ThreadingMixIn, socketserver.TCPServer):
    allow_reuse_address = True
    daemon_threads = True


# =============================================================================
# FLASK APP
# =============================================================================

app = Flask(__name__)

@app.after_request
def add_cors_headers(response):
    response.headers["Access-Control-Allow-Origin"] = "*"
    response.headers["Access-Control-Allow-Methods"] = "GET, POST, OPTIONS"
    response.headers["Access-Control-Allow-Headers"] = "Content-Type, Authorization"
    return response


@app.route("/api/status", methods=["GET"])
def status():
    return jsonify(
        {
            "status": "online",
            "machine": "BioAnalyzer 3000",
            "serial": "BIO-2026-001",
            "version": "2.5.1",
            "transport": {
                "http": {"port": 5000, "status": "active"},
                "tcp": {"port": 5001, "status": "active"},
            },
            "exams_available": list(EXAM_DATABASE.keys()),
            "uptime": datetime.now().isoformat(),
        }
    )


@app.route("/api/exams", methods=["GET"])
def exams():
    serializable_db = {}
    for code, exam in EXAM_DATABASE.items():
        serializable_params = []
        for p in exam["parameters"]:
            sp = {k: v for k, v in p.items()}
            if "derived_from" in sp:
                sp["derived_from"] = sp["derived_from"]
            serializable_params.append(sp)
        serializable_db[code] = {
            "code": exam["code"],
            "name": exam["name"],
            "category": exam["category"],
            "parameters": serializable_params,
        }
    return jsonify({"exams": serializable_db, "total": len(serializable_db)})


@app.route("/api/order", methods=["POST"])
def process_order():
    try:
        data = request.get_json()
        if not data:
            return jsonify({"error": "No JSON data provided"}), 400

        order_id = data.get("order_id", f"ORD-{uuid.uuid4().hex[:8].upper()}")
        exam_code_raw = data.get("exam_code", "GLYC")
        exam_code = exam_code_raw.upper().strip()
        patient = data.get("patient", {})

        if exam_code not in EXAM_DATABASE:
            alias_upper = {k.upper(): v for k, v in CODE_ALIASES.items()}
            if exam_code in alias_upper:
                resolved = alias_upper[exam_code]
                if resolved in EXAM_DATABASE:
                    exam_code = resolved
                    print(f"  [ALIAS] Mapped {exam_code_raw} -> {exam_code}")
            else:
                code_map = {k.upper(): k for k in EXAM_DATABASE.keys()}
                if exam_code in code_map:
                    exam_code = code_map[exam_code]
                else:
                    fallback_code = _resolve_fallback(exam_code_raw)
                    exam_code = fallback_code
                    print(f"  [FALLBACK] Unknown code {exam_code_raw} -> using {exam_code} generic profile")

        if exam_code not in EXAM_DATABASE:
            return (
                jsonify(
                    {
                        "error": f"Unknown exam code: {exam_code_raw}",
                        "available_exams": list(EXAM_DATABASE.keys()),
                    }
                ),
                400,
            )

        sex = patient.get("sex", "M")

        print(f"  [ORDER] Received order {order_id} for {exam_code}")
        print(f"          Patient: {patient.get('name', 'Unknown')} ({sex})")
        print(f"  [PROCESSING] Analyzing {EXAM_DATABASE[exam_code]['name']}...")

        delay = random.uniform(1.5, 4.0)
        time.sleep(delay)

        results = generate_results(exam_code, sex)
        if results is None:
            return jsonify({"error": "Failed to generate results"}), 500

        hl7_response = generate_oru_response(
            {
                "order_id": order_id,
                "exam_code": exam_code,
                "patient": patient,
            },
            results,
        )

        ack_msg = generate_ack(order_id)

        abnormal_count = sum(1 for r in results if r["status"] != "normal")
        status_label = "normal" if abnormal_count == 0 else f"{abnormal_count} abnormal"

        print(
            f"  [RESULT] Completed {order_id}: {len(results)} parameters ({status_label})"
        )

        response_results = []
        for r in results:
            response_results.append(
                {
                    "parameter": r["parameter"],
                    "value": r["value"],
                    "unit": r["unit"],
                    "reference_range": r["reference_range"],
                    "status": r["status"],
                }
            )

        return jsonify(
            {
                "status": "completed",
                "order_id": order_id,
                "exam_code": exam_code,
                "exam_name": EXAM_DATABASE[exam_code]["name"],
                "ack": ack_msg,
                "hl7_response": hl7_response,
                "results": response_results,
                "processing_time_seconds": round(delay, 2),
                "abnormal_count": abnormal_count,
            }
        )

    except Exception as e:
        return jsonify({"error": str(e)}), 500


# =============================================================================
# MAIN
# =============================================================================


def print_banner():
    print()
    print("=" * 65)
    print("  BioAnalyzer 3000 - HL7 v2.5.1 Lab Machine Simulator")
    print("=" * 65)
    print(f"  Machine:    BioAnalyzer 3000")
    print(f"  Serial:     BIO-2026-001")
    print(f"  HL7 Ver:    2.5.1")
    print(f"  Exams:      {len(EXAM_DATABASE)} available")
    print("-" * 65)
    print(f"  HTTP API:   http://0.0.0.0:5000/api/status")
    print(f"  TCP MLLP:   0.0.0.0:5001 (MLLP framed)")
    print("-" * 65)
    print(f"  Endpoints:")
    print(f"    POST /api/order   - Submit lab order")
    print(f"    GET  /api/exams   - List available exams")
    print(f"    GET  /api/status  - Machine health check")
    print("=" * 65)
    print(f"  Ready to accept orders...")
    print()


def start_tcp_server():
    try:
        server = ThreadedTCPServer(("0.0.0.0", 5001), MLLPHandler)
        thread = threading.Thread(target=server.serve_forever, daemon=True)
        thread.start()
        print(f"  [TCP] MLLP server listening on port 5001")
        return server
    except OSError as e:
        print(f"  [TCP] Failed to start: {e}")
        return None


if __name__ == "__main__":
    print_banner()
    tcp_server = start_tcp_server()
    try:
        app.run(host="0.0.0.0", port=5000, debug=False, threaded=True)
    except KeyboardInterrupt:
        print("\n  Shutting down...")
        if tcp_server:
            tcp_server.shutdown()
        sys.exit(0)
