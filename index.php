<?php

include_once('Helpers/token.php');
require_once("Helpers/view.class.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>OpenLinks - Work Type as a Service</title>
  <meta name="description" content="Scale Your Operations with Flexible, Trained Teams â€” One Work Type at a Time" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet" />
   <link rel="icon" href="Images/favicon.ico">
  <style>
    :root {
      --primary: #2563eb;
      --primary-dark: #1e40af;
      --secondary: #eff6ff;
      --light: #f9fafb;
      --dark: #0f172a;
      --muted: #475569;
      --text: #111827;
      --text-light: #ffffff;
      --accent: #f59e0b;
      --accent-dark: #d97706;
      --surface: #ffffff;
      --shadow: 0 10px 30px rgba(2, 8, 23, 0.12);
      --radius: 14px;
      --transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.1);
    }

    * { 
      box-sizing: border-box; 
      margin: 0; 
      padding: 0; 
    }

    html, body { 
      height: 100%; 
      scroll-behavior: smooth;
    }

    body {
      font-family: "Inter", system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, "Helvetica Neue", Arial, "Apple Color Emoji", "Segoe UI Emoji";
      color: var(--text);
      background: radial-gradient(1200px 600px at 90% -10%, #dbeafe 0%, #fff 60%),
                  linear-gradient(180deg, #fff 0%, #f8fafc 100%);
      line-height: 1.6;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
      overflow-x: hidden;
    }

    .text-center { text-align: center; }

    .container { 
      width: 100%; 
      max-width: 1200px; 
      margin-inline: auto; 
      padding-inline: 20px; 
    }

    /* Header */
    header {
      position: sticky; 
      top: 0; 
      z-index: 1000;
      background: rgba(3,32,51,0.98);
      backdrop-filter: blur(8px);
      box-shadow: 0 1px 0 rgba(255,255,255,0.06) inset;
      transition: var(--transition);
    }
    header.scrolled { 
      background: rgba(3,32,51,0.98); 
      box-shadow: 0 4px 18px rgba(2,8,23,0.25);
      padding: 0.5rem 0;
    }

    .nav-container { 
      display: flex; 
      align-items: center; 
      justify-content: space-between; 
      padding: 14px 0; 
      transition: var(--transition);
    }

    .brand { 
      display: flex; 
      align-items: center; 
      gap: 12px; 
      text-decoration: none; 
      color: #fff; 
      font-weight: 700; 
      transition: var(--transition);
    }
    .brand img { 
      height: 56px; 
      width: auto; 
      object-fit: contain; 
      filter: drop-shadow(0 2px 6px rgba(0,0,0,.3));
      transition: var(--transition);
    }
    header.scrolled .brand img {
      height: 46px;
    }

    .nav-menu { 
      list-style: none; 
      display: flex; 
      gap: 28px; 
    }
    .nav-menu a {
      color: #eef2ff; 
      text-decoration: none; 
      font-weight: 500; 
      position: relative; 
      padding: 6px 2px; 
      transition: color .3s ease;
    }
    .nav-menu a::after {
      content: ""; 
      position: absolute; 
      left: 0; 
      bottom: -6px; 
      height: 3px; 
      width: 0; 
      border-radius: 2px; 
      background: linear-gradient(90deg,var(--accent),var(--primary)); 
      transition: width .3s ease;
    }
    .nav-menu a:hover { 
      color: #fff; 
    }
    .nav-menu a:hover::after { 
      width: 100%; 
    }

    .mobile-menu-btn { 
      display: none; 
      background: none; 
      border: none; 
      color: #fff; 
      font-size: 1.9rem; 
      cursor: pointer; 
      z-index: 1001;
      transition: var(--transition);
    }
    .mobile-menu-btn:hover {
      transform: rotate(90deg);
    }

    /* Fixed Buttons Container */
  #button-container {
    position: fixed;
    top: 50%;
    right: 1.5rem;
    transform: translateY(-50%);
    display: flex;
    flex-direction: column;
    gap: 1.2rem;
    z-index: 9999;
  }
  #button-container a {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: #2a9df4;
    color: white;
    padding: 0.7rem 1.3rem;
    border-radius: 30px;
    font-weight: 600;
    font-size: 1rem;
    text-decoration: none;
    box-shadow: 0 5px 15px #1f78d1cc;
    transition: background-color 0.3s ease, box-shadow 0.3s ease, transform 0.2s ease;
  }
  #button-container a svg {
    width: 20px;
    height: 20px;
    fill: white;
    flex-shrink: 0;
  }
  #button-container a:hover {
    background-color: #1f78d1;
    box-shadow: 0 8px 25px #1f78d1dd;
    transform: translateX(5px);
  }

    /* Hero */
    .hero {
      position: relative; 
      text-align: center; 
      color: #fff; 
      overflow: hidden; 
      isolation: isolate;
      background: url('https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1600&q=80') center/cover no-repeat;
    }
    .hero::before {
      content: ""; 
      position: absolute; 
      inset: 0; 
      z-index: 0;
      background:
        radial-gradient(800px 400px at 10% -10%, rgba(37,99,235,.6), transparent 50%),
        linear-gradient(120deg, rgba(3,32,51,.92), rgba(37,99,235,.7));
      animation: gradientShift 8s ease-in-out infinite alternate;
    }
    .hero-content { 
      position: relative; 
      z-index: 1; 
      padding: 140px 0 110px; 
      max-width: 900px; 
      margin: 0 auto; 
    }
    .hero h1 { 
      font-family: "Poppins", sans-serif; 
      font-size: clamp(2.2rem, 5vw, 3.4rem); 
      line-height: 1.15; 
      margin-bottom: 18px; 
      letter-spacing: .2px; 
      animation: fadeInUp 1s ease-out;
    }
    .hero p { 
      font-size: clamp(1rem, 2.2vw, 1.2rem); 
      color: #e5e7eb; 
      margin-bottom: 34px; 
      animation: fadeInUp 1s ease-out 0.2s both;
    }
    .hero .btn {
      animation: fadeInUp 1s ease-out 0.4s both;
    }

    /* Buttons */
    .btn { 
      display: inline-block; 
      background: linear-gradient(90deg, var(--primary), var(--primary-dark)); 
      color: #fff; 
      padding: 14px 28px; 
      border-radius: var(--radius); 
      font-weight: 600; 
      text-decoration: none; 
      border: none; 
      cursor: pointer; 
      box-shadow: var(--shadow); 
      transition: var(--transition);
      position: relative;
      overflow: hidden;
    }
    .btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
      transition: 0.5s;
    }
    .btn:hover::before {
      left: 100%;
    }
    .btn:hover { 
      transform: translateY(-3px) scale(1.02); 
      box-shadow: 0 18px 30px rgba(2,8,23,.25); 
      filter: brightness(1.05); 
    }
    .btn-outline { 
      background: transparent; 
      color: var(--primary); 
      border: 2px solid var(--primary); 
    }
    .btn-outline:hover { 
      background: var(--primary); 
      color: #fff; 
    }

    /* Section base */
    section { 
      padding: 90px 0; 
      position: relative;
    }
    h2 { 
      font-family: "Poppins", sans-serif; 
      font-size: clamp(1.6rem, 3vw, 2.4rem); 
      color: var(--primary-dark); 
      text-align: center; 
      margin-bottom: 12px; 
      position: relative;
      display: inline-block;
    }
    h2::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      width: 60px;
      height: 4px;
      background: linear-gradient(90deg, var(--accent), var(--primary));
      border-radius: 2px;
    }
    .section-lead { 
      text-align: center; 
      color: var(--muted); 
      max-width: 820px; 
      margin: 24px auto 36px; 
    }

    /* Grids */
    .grid { 
      display: grid; 
      gap: 28px; 
      margin-top: 28px; 
    }
    .grid-3 { 
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); 
    }
    .grid-4 { 
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); 
    }

    /* Cards */
    .card { 
      background: rgba(255,255,255,0.9); 
      backdrop-filter: blur(6px); 
      border-radius: var(--radius); 
      padding: 26px; 
      box-shadow: var(--shadow); 
      transition: var(--transition); 
      position: relative; 
      overflow: hidden; 
      border: 1px solid rgba(255,255,255,0.5);
    }
    .card::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 5px;
      background: linear-gradient(90deg, var(--accent), var(--primary));
      transform: scaleX(0);
      transform-origin: left;
      transition: transform 0.5s ease;
    }
    .card::after { 
      content: ""; 
      position: absolute; 
      inset: 0; 
      background: radial-gradient(500px 140px at -10% -10%, rgba(37,99,235,.08), transparent 40%); 
      pointer-events: none; 
      opacity: 0;
      transition: opacity 0.5s ease;
    }
    .card:hover::before {
      transform: scaleX(1);
    }
    .card:hover::after {
      opacity: 1;
    }
    .card:hover { 
      transform: translateY(-6px) scale(1.02); 
      box-shadow: 0 18px 38px rgba(2,8,23,.18); 
    }
    .card h3 { 
      color: var(--primary); 
      margin-bottom: 8px; 
      font-size: 1.2rem; 
    }

    /* Highlights/Chips */
    .highlight { 
      background: linear-gradient(120deg, var(--primary), var(--primary-dark)); 
      color: #fff; 
      padding: 18px; 
      border-radius: 999px; 
      text-align: center; 
      box-shadow: var(--shadow); 
      font-weight: 600; 
      letter-spacing: .2px; 
      transition: var(--transition);
      cursor: default;
      position: relative;
      overflow: hidden;
    }
    .highlight::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(120deg, transparent, rgba(255,255,255,0.2), transparent);
      transform: translateX(-100%);
    }
    .highlight:hover::before {
      animation: shine 1s;
    }
    .highlight:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 25px rgba(37, 99, 235, 0.3);
    }

    /* Section backgrounds */
    .bg-light { background: var(--light); }
    .bg-secondary { background: var(--secondary); }

    /* Registered Work Types */
    .section-shell { 
      background: linear-gradient(120deg, #eff6ff 0%, #e6eef9 100%); 
      border-radius: var(--radius); 
      box-shadow: var(--shadow); 
      padding: 40px 22px; 
      margin: 40px auto; 
      max-width: 1100px; 
      position: relative;
      overflow: hidden;
    }
    .section-shell::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
      transform: rotate(30deg);
      pointer-events: none;
    }
    .section-shell h2 { 
      color: #032033; 
      margin-bottom: 10px; 
    }
    .section-shell p { 
      text-align: center; 
      margin-bottom: 18px; 
      color: #2563eb; 
      font-weight: 600; 
    }
    .section-shell table { 
      width: 100%; 
      border-collapse: collapse; 
      background: #fff; 
      border-radius: 12px; 
      overflow: hidden; 
      box-shadow: 0 8px 22px rgba(2,8,23,.06);
      transition: var(--transition);
    }
    .section-shell table:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 30px rgba(2,8,23,.1);
    }
    .section-shell th, .section-shell td { 
      padding: 14px 12px; 
      text-align: center; 
      border-bottom: 1px solid #e5e7eb; 
      font-size: 1rem; 
      transition: var(--transition);
    }
    .section-shell th { 
      background: var(--primary); 
      color: #fff; 
      letter-spacing: .3px; 
      position: relative;
    }
    .section-shell th::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 2px;
      background: rgba(255,255,255,0.3);
    }
    .section-shell tbody tr:nth-child(odd) { 
      background: #f8fbff; 
    }
    .section-shell tbody tr:hover { 
      background: #eff6ff; 
      transform: scale(1.01);
    }
    .section-shell tbody tr {
      transition: var(--transition);
    }

   
    .form-field { 
      flex: 1; 
      min-width: 220px; 
      position: relative;
    }
    .form-field label { 
      display: block; 
      font-weight: 600; 
      margin-bottom: 8px; 
      color: #e2e8f0; 
    }
    .form-field select, .form-field input { 
      width: 100%; 
      padding: 12px 14px; 
      border-radius: 10px; 
      border: 1px solid rgba(255,255,255,.15); 
      background: rgba(255,255,255,.06); 
      color: #fff; 
      outline: none; 
      transition: var(--transition);
    }
    .form-field select:focus, .form-field input:focus { 
      border-color: #60a5fa; 
      background: rgba(255,255,255,.12); 
      box-shadow: 0 0 0 3px rgba(96,165,250,0.2);
    }

    .unit-inputs { 
      display: grid; 
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); 
      gap: 14px; 
      margin-top: 14px; 
      position: relative;
      z-index: 1;
    }
    .unit-input { 
      display: flex; 
      align-items: center; 
      justify-content: space-between; 
      gap: 10px; 
      background: rgba(255,255,255,.06); 
      border: 1px solid rgba(255,255,255,.12); 
      border-radius: 10px; 
      padding: 10px 12px; 
      transition: var(--transition);
    }
    .unit-input:hover {
      background: rgba(255,255,255,.1);
      border-color: rgba(255,255,255,.2);
    }
    .unit-input label { 
      color: #cbd5e1; 
    }
    .unit-input input { 
      width: 110px; 
      text-align: right; 
      padding: 10px 12px; 
      border-radius: 8px; 
      border: 1px solid rgba(255,255,255,.18); 
      background: rgba(255,255,255,.08); 
      color: #fff; 
      transition: var(--transition);
    }
    .unit-input input:focus {
      border-color: #60a5fa;
      box-shadow: 0 0 0 2px rgba(96,165,250,0.2);
    }

    .actions { 
      display: flex; 
      gap: 12px; 
      align-items: center; 
      margin-top: 16px; 
      position: relative;
      z-index: 1;
    }
    .btn-ghost { 
      background: transparent; 
      color: #e2e8f0; 
      border: 1px dashed rgba(255,255,255,.25); 
    }
    .btn-ghost:hover { 
      background: rgba(255,255,255,.06); 
      border-style: solid;
    }

    #earnings-table { 
      width: 100%; 
      border-collapse: collapse; 
      margin-top: 22px; 
      color: #e2e8f0; 
      display: none; 
      opacity: 0; 
      transform: translateY(12px); 
      transition: var(--transition);
      position: relative;
      z-index: 1;
    }
    #earnings-table.show { 
      display: table; 
      opacity: 1; 
      transform: translateY(0); 
      animation: tableFadeIn 0.5s ease-out;
    }
    #earnings-table th { 
      background: #1f8ef1; 
      color: #fff; 
      padding: 12px; 
      text-align: left; 
    }
    #earnings-table td { 
      padding: 12px; 
      border-bottom: 1px solid rgba(255,255,255,.12); 
    }
    #earnings-table tr:nth-child(even) { 
      background: rgba(255,255,255,.05); 
    }
    #earnings-table tr:nth-child(odd) { 
      background: rgba(255,255,255,.09); 
    }
    #earnings-table tr:last-child { 
      background: #1f8ef1; 
      font-weight: 700; 
    }
    #earnings-table tr {
      transition: var(--transition);
    }
    #earnings-table tr:hover {
      background: rgba(255,255,255,0.12);
    }

    /* Contact Form */
    #contactForm {
      transition: var(--transition);
    }
    #contactForm:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 35px rgba(2,8,23,.15);
    }
    #contactForm input, #contactForm textarea {
      transition: var(--transition);
      border: 1px solid #e5e7eb;
    }
    #contactForm input:focus, #contactForm textarea:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(37,99,235,0.2);
      transform: translateY(-2px);
    }

    /* Footer */
    footer { 
      background: linear-gradient(135deg, #032033, #032033); 
      color: #e5e7eb; 
      padding: 70px 0 40px; 
      margin-top: 60px; 
      position: relative;
      overflow: hidden;
    }
    footer::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23374151' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
      opacity: 0.3;
    }
    .footer-grid { 
      display: grid; 
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); 
      gap: 40px; 
      position: relative;
      z-index: 1;
    }
    .footer-heading { 
      color: #fff; 
      margin-bottom: 12px; 
      font-weight: 600; 
    }
    .footer-links { 
      list-style: none; 
    }
    .footer-links li {
      margin-bottom: 8px;
      transition: var(--transition);
    }
    .footer-links li:hover {
      transform: translateX(5px);
    }
    .footer-links a { 
      color: #d1d5db; 
      text-decoration: none; 
      transition: var(--transition);
      display: inline-block;
    }
    .footer-links a:hover { 
      color: #fff; 
      transform: translateX(3px);
    }
    .copyright { 
      text-align: center; 
      padding-top: 26px; 
      border-top: 1px solid #374151; 
      color: #94a3b8; 
      margin-top: 26px; 
      position: relative;
      z-index: 1;
    }

    /* Animations */
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes gradientShift {
      0% {
        background-position: 0% 50%;
      }
      100% {
        background-position: 100% 50%;
      }
    }

    @keyframes shine {
      to {
        transform: translateX(100%);
      }
    }

    @keyframes tableFadeIn {
      0% {
        opacity: 0;
        transform: translateY(20px);
      }
      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Scroll reveal */
    .reveal { 
      opacity: 0; 
      transform: translateY(26px); 
      transition: opacity .6s ease, transform .6s ease; 
    }
    .reveal.show { 
      opacity: 1; 
      transform: translateY(0); 
    }

    /* Responsive */
    @media (max-width: 900px) {
      .hero-content { padding: 110px 0 90px; }
    }

    @media (max-width: 768px) {
      .nav-menu { 
        position: fixed; 
        top: 0; 
        left: -100%; 
        flex-direction: column; 
        background: #032033; 
        width: 80%; 
        height: 100vh; 
        padding: 100px 20px 20px; 
        gap: 18px; 
        box-shadow: var(--shadow); 
        transition: var(--transition);
        z-index: 999;
      }
      .nav-menu.active { 
        left: 0; 
        box-shadow: 10px 0 30px rgba(0,0,0,0.3);
      }
      .mobile-menu-btn { 
        display: block; 
        z-index: 1000;
      }
      section { padding: 70px 0; }
      .grid-3, .grid-4 { grid-template-columns: 1fr; }
      .earnings-calculator { padding: 26px 16px; }
      
      /* Mobile menu animation */
      @keyframes slideIn {
        from { transform: translateX(-100%); }
        to { transform: translateX(0); }
      }
      .nav-menu.active {
        animation: slideIn 0.3s ease-out;
      }
    }

    /* Popup Overlay Styling */
.popup-overlay {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(37,99,235,0.12);
  z-index: 9999;
  align-items: center;
  justify-content: center;
  transition: opacity 0.3s;
}
.popup-overlay.active {
  display: flex;
  opacity: 1;
}
.popup-content {
  background: #fff;
  border-radius: 18px;
  box-shadow: 0 8px 32px rgba(37,99,235,0.18);
  padding: 32px 24px;
  min-width: 340px;
  max-width: 95vw;
  position: relative;
  animation: fadeInUp 0.4s;
}
.popup-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 18px;
}
.popup-title {
  color: #2563eb;
  font-size: 1.35rem;
  font-weight: 700;
  margin: 0;
}
.close-btn {
  background: none;
  border: none;
  font-size: 1.6rem;
  color: #2563eb;
  cursor: pointer;
  transition: color 0.2s;
}
.close-btn:hover {
  color: #f59e0b;
}
.team-table {
  width: 100%;
  border-collapse: collapse;
  background: #f8fafc;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 2px 12px rgba(37,99,235,0.07);
}
.team-table th, .team-table td {
  padding: 10px 12px;
  text-align: left;
  border-bottom: 1px solid #e5e7eb;
  font-size: 1rem;
}
.team-table th {
  background: #2563eb;
  color: #fff;
  font-weight: 600;
  letter-spacing: 0.5px;
}
.team-table tr:last-child td {
  border-bottom: none;
}
.team-table td.address-cell {
  font-size: 0.97rem;
  color: #475569;
}
@media (max-width: 600px) {
  .popup-content {
    padding: 18px 6px;
    min-width: 0;
  }
  .team-table th, .team-table td {
    padding: 8px 6px;
    font-size: 0.95rem;
  }
}
.view-team-btn, .open-table-btn {
background: linear-gradient(90deg, #f59e0b 10%, #2563eb 80%);
  color: #fff;
  font-weight: 600;
  border: none;
  border-radius: 10px;
  padding: 10px 22px;
  box-shadow: 0 2px 12px rgba(37,99,235,0.09);
  cursor: pointer;
  font-size: 1rem;
  letter-spacing: 0.5px;
  transition: 
    background 0.3s cubic-bezier(.4,0,.2,1),
    transform 0.2s cubic-bezier(.4,0,.2,1),
    box-shadow 0.3s cubic-bezier(.4,0,.2,1);
  position: relative;
  overflow: hidden;
}
.view-team-btn::after, .open-table-btn::after {
  content: '';
  position: absolute;
  left: 0; top: 0;
  width: 100%; height: 100%;
  background: linear-gradient(90deg, rgba(245,158,11,0.18), transparent 70%);
  opacity: 0;
  transition: opacity 0.4s;
  pointer-events: none;
}
.view-team-btn:hover, .open-table-btn:hover {
  background: linear-gradient(90deg, #f59e0b);
  color: #032033;
  transform: translateY(-2px) scale(1.04);
  box-shadow: 0 8px 24px rgba(245,158,11,0.13);
}
.view-team-btn:hover::after, .open-table-btn:hover::after {
  opacity: 1;
}
.view-team-btn:active, .open-table-btn:active {
   background: linear-gradient(90deg, #f59e0b 60%, #2563eb 100%);
  color: #fff;
  transform: scale(0.98);
}
  </style>
</head>

<body>
  <!-- Header & Navigation -->
  <!-- Header & Navigation -->
  <header id="siteHeader">
    <div class="container nav-container">
      <a class="brand" href="https://openlinks.co.za" rel="noopener">
        <img id="oplLogo" src="Images/logo-removebg-preview.png" alt="OpenLinks Logo" style="height:60px; width: 200px;">
      </a>
      
     <button class="mobile-menu-btn" aria-label="Open Menu">☰</button>
      
        <ul class="nav-menu">
         <li><a href="https://openlinks.co.za">WTaaS Services</a></li>
        <li><a href="subpage1.html">WTaaS Orignators</a></li>
        <li><a href="subpage2.html">Rollout Plans</a></li>
        <li><a href="specialised.html">Specialised Industry</a></li>
          <li><a href="backing_structure.html">WT Backing Structure</a></li>
        <li><a href="About_us.html">About Us</a></li>
      </ul>
    </div>
  </header>


   

   <!-- <a href="https://openlinks.co.za/TMS/login.php" target="_blank" rel="noopener noreferrer">
    <svg viewBox="0 0 24 24"><path d="M10.09 15.59L8.67 14.17 12.34 10.5H3v-2h9.34L8.67 5.83 10.09 4.41 16.67 11z"/></svg> Login to OpenInLinks
  </a> -->
  <!-- Hero -->
  <section class="hero">
    <div class="container hero-content">
      <h1>Work Type as a Service (WTaaS)</h1>
      <p>Scale your operations with flexible, trained teams one work type at a time.</p>
      <a href="#registered" class="btn">Experience OpenLink today Contact Us</a>
    </div>
  </section>

  <!-- What is WTaaS -->
  <section id="what-is-wtaas" class="bg-secondary">
    <div class="container">
      <h2 class="reveal">What is WTaaS?</h2>
      <p class="section-lead reveal" style="font-size: 20px !important;">
        WTaaS is your flexible workforce partner delivering ready trained teams for your specific tasks whenever and wherever you need them. No long hiring cycles, no overhead just skilled people doing the job right.
      </p>
    </div>
  </section>

 <section id="how-it-works" style="background: linear-gradient(to bottom, #ffffff, #eff6ff); padding: 40px 0;">
  <div class="container">
    <h2 class="reveal">How It Works</h2>
    <div class="grid grid-3">
      <div class="card reveal">
        <h3>Work Type Analysis</h3>
        <p>We identify and standardize your key tasks through a comprehensive analysis of your operational needs.</p>
      </div>
      <div class="card reveal">
        <h3>Deployment-Ready Teams</h3>
        <p>We supply trained teams specifically aligned to those tasks, ready to integrate with your workflow.</p>
      </div>
      <div class="card reveal">
        <h3>Trial & Validate</h3>
        <p>Test pilot teams, review live data, and refine processes before committing to a full rollout.</p>
      </div>
    </div>
  </div>
</section>


  <!-- Benefits -->
  <section id="benefits" class="bg-light" style="background: linear-gradient(to bottom, #ffffff, #eff6ff); padding: 40px 0;">
    <div class="container">
      <h2 class="reveal">Why Choose WTaaS?</h2>
      <div class="grid grid-4">
        <div class="card text-center reveal">
          <h3>Scale Lean</h3>
          <p>Grow operations without the overhead of permanent hires or lengthy recruitment.</p>
        </div>
        <div class="card text-center reveal">
          <h3>Reduce Errors</h3>
          <p>Specialists focused on specific tasks deliver higher quality with fewer mistakes.</p>
        </div>
        <div class="card text-center reveal">
          <h3>Focus Your Core Team</h3>
          <p>Free up internal resources to focus on strategic initiatives.</p>
        </div>
        <div class="card text-center reveal">
          <h3>Track Progress</h3>
          <p>Reporting and analytics to monitor performance and ROI.</p>
        </div>
      </div>
    </div>
  </section>
  <!-- Registered Work Types -->
  <section class="section-shell" style="background: linear-gradient(120deg, #eff6ff 0%, #e6eef9 100%) !important" id="registered">
    <div class="container">
      <h2 class="reveal">Registered Work Types</h2>
      <p class="reveal">Below are examples of both in-house and originator-submitted work types:</p>
      <div class="reveal">
    <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Work Type</th>
            <th>No of Resources</th>
            <th>Target</th>
            <th>Source</th>
            <th>Contact Team</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1</td>
            <td>Sourcing And Procurement Services</td>
            <td>3</td>
            <td>200</td>
            <td>Inhouse</td>
            <td><button class="view-team-btn" data-product="WebSuite Pro">Get Contacts</button></td>
          </tr>
          <tr>
            <td>2</td>
            <td>Preferential Procurement Services</td>
            <td>8</td>
            <td>500</td>
            <td>Inhouse</td>
            <td><button class="view-team-btn" data-product="DataAnalyzer 360">Get Contacts</button></td>
          </tr>
          <tr>
            <td>3</td>
            <td>Value Chain Planning</td>
            <td>16</td>
            <td>500</td>
            <td>Inhouse</td>
            <td><button class="view-team-btn" data-product="CloudSecure Backup">Get Contacts</button></td>
          </tr>
          <tr>
            <td>4</td>
            <td>OpenLinks Is Your Strategic Partner In Enterprise Development</td>
            <td>7</td>
            <td>500</td>
            <td>Inhouse</td>
            <td><button class="view-team-btn" data-product="MobileOffice Suite">Get Contacts</button></td>
          </tr>
          <tr>
            <td>5</td>
            <td>Large-Scale Commodity Deal Service</td>
            <td>4</td>
            <td>500</td>
            <td>Orginator</td>
    <td><button class="view-team-btn" data-product="MobileOffice Suite">Get Contacts</button></td>
          </tr>
          <tr>
            <td>6</td>
            <td>Easy RQF Coming Your Way</td>
            <td>3</td>
            <td>100</td>
            <td>Inhouse</td>
           <td><button class="view-team-btn" data-product="MobileOffice Suite">Get Contacts</button></td>
          </tr>
          <tr>
            <td>7</td>
            <td>Commodity Suppliers</td>
            <td>3</td>
            <td>500</td>
            <td>Orginator</td>
          <td><button class="view-team-btn" data-product="MobileOffice Suite">Get Contacts</button></td>
          </tr>
          <tr>
            <td>8</td>
            <td>Bill Of Material</td>
            <td>2</td>
            <td>10</td>
            <td>Orginator</td>
           <td><button class="view-team-btn" data-product="MobileOffice Suite">Get Contacts</button></td>
          </tr>
          <tr>
            <td>9</td>
            <td>Commodity Distribution Channel</td>
            <td>3</td>
            <td>500</td>
            <td>Orginator</td>
           <td><button class="view-team-btn" data-product="MobileOffice Suite">Get Contacts</button></td>
          </tr>
          <tr>
            <td>10</td>
            <td>Training Record Drive</td>
            <td>3</td>
            <td>100</td>
            <td>Orginator</td>
       <td><button class="view-team-btn" data-product="MobileOffice Suite">Get Contacts</button></td>
          </tr>
          <tr>
            <td>11</td>
            <td>Presence &amp; Availability Management - Leadership Work Type</td>
            <td>1</td>
            <td>120</td>
            <td>Inhouse</td>
         <td><button class="view-team-btn" data-product="MobileOffice Suite">Get Contacts</button></td>
          </tr>
          <tr>
            <td>12</td>
            <td>Daily Allocation – Leadership Work Type</td>
            <td>1</td>
            <td>144</td>
            <td>Inhouse</td>
         <td><button class="view-team-btn" data-product="MobileOffice Suite">Get Contacts</button></td>
          </tr>
          <tr>
            <td>13</td>
            <td>Instruction: Progress Monitoring (Midday Check – Progress Control &amp; Adjustment) – Leadership Work Type</td>
            <td>1</td>
            <td>72</td>
            <td>Inhouse</td>
          <td><button class="view-team-btn" data-product="MobileOffice Suite">Get Contacts</button></td>
          </tr>
          <tr>
            <td>14</td>
            <td>End-of-Day Close-Out &amp; Daily Records Management – Leadership Work Type</td>
            <td>2</td>
            <td>144</td>
            <td>Inhouse</td>
         <td><button class="view-team-btn" data-product="MobileOffice Suite">Get Contacts</button></td>
          </tr>
          <tr>
            <td>15</td>
            <td>5-Day Competence-Based Training Program (30 People Per Session)</td>
            <td>2</td>
            <td>150</td>
            <td>Orginator</td>
        <td><button class="view-team-btn" data-product="MobileOffice Suite">Get Contacts</button></td>
          </tr>
        </tbody>
      </table>
      </div>
    </div>
  </section>
 <!-- Popup Template -->
    <div class="popup-overlay" id="teamPopup">
        <div class="popup-content">
            <div class="popup-header">
                <h2 class="popup-title" id="popupProductName">Product Team</h2>
                <button class="close-btn">&times;</button>
            </div>
            <table class="team-table" id="teamTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Team data will be inserted here by JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
 

 <section id="services" class="bg-secondary" style="position: relative; background-image: url('https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?ixlib=rb-4.0.3&auto=format&fit=crop&w=1500&q=80'); background-size: cover; background-position: center; background-repeat: no-repeat; color: white; z-index: 1;">
  
  <!-- Overlay -->
  <div style="position:absolute; inset:0; background:rgba(0,51,153,0.6); z-index:-1;"></div>

  <div class="container text-center" style="position: relative; z-index: 1;">
    <h2 class="reveal" style="color: rgb(172, 236, 244);">Explore More WTaaS Services</h2>
    <div style="margin-top: 26px; display:flex; gap:12px; flex-wrap:wrap; justify-content:center" class="reveal">
      <a href="subpage1.html" class="btn btn-outline" style="color: rgb(172, 236, 244);">Partner with Work Type Originators</a>
      <a href="subpage2.html" class="btn btn-outline" style="color: rgb(172, 236, 244);">Get to learn Rolls Out Plans</a>
      <a href="subpage3.html" class="btn btn-outline" style="color: rgb(172, 236, 244);">WTaaS for Specialed Industry</a>
    </div>
  </div>
</section>

<!-- Fixed Buttons -->
<!--<div id="button-container" role="region" aria-label="Quick access buttons">-->
<!--    <p>Login</p>-->
<!--  <a href="https://openlinks.co.za/TIMS/ADMIN/login.php" target="_blank" rel="noopener noreferrer" aria-label="Login to OpenInLinks Platform" style="background: linear-gradient(90deg, #f59e0b 10%, rgba(0,51,153,0.6) 50%);">-->
<!--    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="currentColor"><path d="M10.09 15.59L8.67 14.17 12.34 10.5H3v-2h9.34L8.67 5.83 10.09 4.41 16.67 11z"/></svg>-->
<!--   Admin_Op-->
<!--  </a>-->
<!--   <a href="https://openlinks.co.za/TIMS/ADMIN/login.php" target="_blank" rel="noopener noreferrer" aria-label="Login to OpenInLinks Platform" style="background: linear-gradient(90deg, #f59e0b 10%, rgba(0,51,153,0.6) 50%);">-->
<!--    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="currentColor"><path d="M10.09 15.59L8.67 14.17 12.34 10.5H3v-2h9.34L8.67 5.83 10.09 4.41 16.67 11z"/></svg>-->
<!--    Admin_TMS-->
<!--  </a>-->
<!--</div>-->


<!-- Fixed Buttons -->
<div id="button-container" role="region" aria-label="Quick access buttons">
  <div class="dropdown">
    <button class="dropdown-toggle" aria-haspopup="true" aria-expanded="false" aria-label="Login options" 
      style="display: flex; align-items: center; gap: 0.5rem; color: white; padding: 0.7rem 1.3rem; border-radius: 30px; font-weight: 600; font-size: 1rem; border: none; cursor: pointer; box-shadow: 0 5px 15px #1f78d1cc; background: linear-gradient(90deg, #f59e0b 10%, rgba(0,51,153,0.6) 50%)">
      <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="currentColor" style="width: 20px; height: 20px;">
        <path d="M10.09 15.59L8.67 14.17 12.34 10.5H3v-2h9.34L8.67 5.83 10.09 4.41 16.67 11z"/>
      </svg>
      Login
      <span style="margin-left: 8px; font-size: 0.5em;">▼</span>
    </button>
    <div class="dropdown-menu" 
      style="display: none; flex-direction: column; gap: 0.5rem; border-radius: 14px; box-shadow: 0 8px 32px rgba(37,99,235,0.18); position: absolute; right: 0; top: 110%; width: 120px; z-index: 10000; padding: 10px 0; background: linear-gradient(90deg, #f59e0b 10%, rgba(0,51,153,0.6) 50%)">

      <a href="https://openlinks.co.za/TIMS/ADMIN/login.php" 
        aria-label="Login to OpenInLinks Platform"
        style="display: flex; align-items: center; gap: 0.5rem; color: white; background: none; padding: 10px 18px; border-radius: 0; font-weight: 600; font-size: 0.7rem; text-decoration: none;">
        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="currentColor" style="width: 20px; height: 20px;">
          <path d="M10.09 15.59L8.67 14.17 12.34 10.5H3v-2h9.34L8.67 5.83 10.09 4.41 16.67 11z"/>
        </svg>
        Ops
      </a>

      <a href="https://openlinks.co.za/TMS/login.php" 
        aria-label="Login to OpenInLinks Platform"
        style="display: flex; align-items: center; gap: 0.5rem; color: white; background: none; padding: 10px 18px; border-radius: 0; font-weight: 600; font-size: 0.7rem; text-decoration: none;">
        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="currentColor" style="width: 20px; height: 20px;">
          <path d="M10.09 15.59L8.67 14.17 12.34 10.5H3v-2h9.34L8.67 5.83 10.09 4.41 16.67 11z"/>
        </svg>
        TMS
      </a>

    </div>
  </div>
</div>




  <!-- Contact -->
  <section id="contact">
    <div class="container">
      <h2 class="reveal">Ready to scale with WTaaS?</h2>
      <p class="section-lead reveal">Contact us today for a free consultation and discover how our WTaaS can transform your operations.</p>

      <div class="container" style="max-width: 700px">
        <form action="https://formspree.io/f/xnnbdbrg" method="POST" class="card reveal">
          <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
                <input type="text" id="title" name="title" hidden="hidden" value="Hi Team Find Request for information from Channel WTaaS Service" />
            <div>
              <label for="name" style="display:block; font-weight:600; margin-bottom:8px;">Full Name</label>
              <input type="text" id="name" name="name" required style="width:100%; padding:12px 14px; border-radius:10px; border:1px solid #e5e7eb;" />
            </div>
            <div>
              <label for="email" style="display:block; font-weight:600; margin-bottom:8px;">Email Address</label>
              <input type="email" id="email" name="email" required style="width:100%; padding:12px 14px; border-radius:10px; border:1px solid #e5e7eb;" />
            </div>
             <div>
              <label for="email" style="display:block; font-weight:600; margin-bottom:8px;">Phone</label>
              <input type="number" id="phone" name="phone" required style="width:100%; padding:12px 14px; border-radius:10px; border:1px solid #e5e7eb;" />
            </div>
          </div>
          <div style="margin-top:14px;">
            <label for="company" style="display:block; font-weight:600; margin-bottom:8px;">Company Name</label>
            <input type="text" id="company" name="company" style="width:100%; padding:12px 14px; border-radius:10px; border:1px solid #e5e7eb;" />
          </div>
          <div style="margin-top:14px;">
            <label for="message" style="display:block; font-weight:600; margin-bottom:8px;">How can we help?</label>
            <textarea id="message" rows="5" name="message" required style="width:100%; padding:12px 14px; border-radius:10px; border:1px solid #e5e7eb;"></textarea>
          </div>
          <button type="submit" class="btn" style="margin-top:16px; width:100%;font-size: 20px !important;">Submit Request</button>
        </form>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer>
    <div class="container">
      <div class="footer-grid">
        <div>
          <h3 class="footer-heading">OpenLinks WTaaS</h3>
          <p>Work Type as a Service solutions designed to scale your operations with flexible, trained teams.</p>
        </div>
          <div>
          <h3 class="footer-heading">Quick Links</h3>
          <ul class="footer-links">
          <li><a href="https://openlinks.co.za">WTaaS Services</a></li>
        <li><a href="subpage1.html">WTaaS Orignators</a></li>
        <li><a href="subpage2.html">Rollout Plans</a></li>
        <li><a href="subpage3.html">Specialed Industry</a></li>
          <li><a href="subpage4.html">WT Backing Structure</a></li>
        <li><a href="About_us.html">About Us</a></li>
          </ul>
        </div>
        
        <div>
          <h3 class="footer-heading">Contact Us</h3>
          <ul class="footer-links">
            <li><a href="tel:+27410040454">Office: 041 004 0454</a></li>
            <li><a href="tel:+27739923906">Mobile: 073 992 3906</a></li>
            <li><a href="mailto:venas@openlinks.co.za">venas@openlinks.co.za</a></li>
          </ul>
        </div>
      </div>
      <div class="copyright">© 2025 OpenLinks. All rights reserved.</div>
    </div>
  </footer>



  <script>
    // Header shrink
    const headerEl = document.getElementById('siteHeader');
    window.addEventListener('scroll', () => headerEl.classList.toggle('scrolled', window.scrollY > 6));

    // Mobile menu toggle
    const menuBtn = document.querySelector('.mobile-menu-btn');
    const navMenu = document.querySelector('.nav-menu');
    menuBtn.addEventListener('click', () => {
      navMenu.classList.toggle('active');
      menuBtn.textContent = navMenu.classList.contains('active') ? '☰' : '|||';
    });

    // Close mobile menu when clicking outside
    document.addEventListener('click', (e) => {
      if (navMenu.classList.contains('active') && 
          !navMenu.contains(e.target) && 
          !menuBtn.contains(e.target)) {
        navMenu.classList.remove('active');
        menuBtn.textContent = '|||';
      }
    });

    // Smooth scroll for internal links
    document.querySelectorAll('a[href^="#"]').forEach(a => {
      a.addEventListener('click', (e) => {
        const href = a.getAttribute('href');
        if (href.length > 1) {
          e.preventDefault();
          const el = document.querySelector(href);
          if (el) { 
            el.scrollIntoView({ behavior: 'smooth', block: 'start' }); 
          }
          navMenu.classList.remove('active');
          menuBtn.textContent = '☰';
        }
      });
    });

    // Scroll reveal
    const io = new IntersectionObserver((entries) => {
      entries.forEach((entry) => { 
        if (entry.isIntersecting) {
          entry.target.classList.add('show');
          // Add staggered animation for grid items
          if (entry.target.classList.contains('grid')) {
            const items = entry.target.querySelectorAll('.card, .highlight');
            items.forEach((item, index) => {
              item.style.transitionDelay = `${index * 0.1}s`;
            });
          }
        }
      });
    }, { threshold: 0.18 });
    document.querySelectorAll('.reveal').forEach((el) => io.observe(el));

    
    // Contact form submit (demo only)
    const contactForm = document.getElementById('contactForm');
    contactForm?.addEventListener('submit', (e) => {
      e.preventDefault();
      alert('Thank you for your interest! We will contact you shortly.');
      contactForm.reset();
    });

        // Team data for each product
        const teamData = {
            "WebSuite Pro": [
                {
                    name: "Sarah Johnson",
                    role: "Lead Developer",
                    email: "sarah@example.com",
                    phone: "+1 (555) 123-4567",
                    address: "123 Tech Blvd, San Francisco, CA 94103"
                },
                {
                    name: "Michael Chen",
                    role: "UI/UX Designer",
                    email: "michael@example.com",
                    phone: "+1 (555) 987-6543",
                    address: "456 Design Ave, Oakland, CA 94610"
                },
                {
                    name: "Emily Rodriguez",
                    role: "QA Specialist",
                    email: "emily@example.com",
                    phone: "+1 (555) 456-7890",
                    address: "789 Quality St, Berkeley, CA 94704"
                }
            ],
            "DataAnalyzer 360": [
                {
                    name: "David Wilson",
                    role: "Data Scientist",
                    email: "david@example.com",
                    phone: "+1 (555) 234-5678",
                    address: "321 Data Dr, San Jose, CA 95112"
                },
                {
                    name: "Lisa Thompson",
                    role: "Business Analyst",
                    email: "lisa@example.com",
                    phone: "+1 (555) 876-5432",
                    address: "654 Analytics Ln, Sunnyvale, CA 94086"
                }
            ],
            "CloudSecure Backup": [
                {
                    name: "Robert Miller",
                    role: "Security Expert",
                    email: "robert@example.com",
                    phone: "+1 (555) 345-6789",
                    address: "987 Security Way, Santa Clara, CA 95050"
                },
                {
                    name: "Jennifer Lee",
                    role: "Network Engineer",
                    email: "jennifer@example.com",
                    phone: "+1 (555) 765-4321",
                    address: "876 Cloud St, Mountain View, CA 94039"
                }
            ],
            "MobileOffice Suite": [
                {
                    name: "James Anderson",
                    role: "Mobile Developer",
                    email: "james@example.com",
                    phone: "+1 (555) 456-7890",
                    address: "543 Mobile Ave, Palo Alto, CA 94301"
                },
                {
                    name: "Amanda White",
                    role: "Product Manager",
                    email: "amanda@example.com",
                    phone: "+1 (555) 654-3210",
                    address: "210 Office Rd, Menlo Park, CA 94025"
                }
            ]
        };

        // DOM elements
        const popupOverlay = document.getElementById('teamPopup');
        const popupProductName = document.getElementById('popupProductName');
        const teamTableBody = document.querySelector('#teamTable tbody');
        const closeBtn = document.querySelector('.close-btn');
        const viewTeamBtns = document.querySelectorAll('.view-team-btn');

        // Function to open popup with team data
        function openTeamPopup(productName) {
            const team = teamData[productName];
            
            if (!team) return;
            
            // Set product name in popup
            popupProductName.textContent = `${productName} Support Team`;
            
            // Clear previous team data
            teamTableBody.innerHTML = '';
            
            // Add team members to table
            team.forEach(member => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${member.name}</td>
                    <td>${member.role}</td>
                    <td>${member.email}</td>
                    <td>${member.phone}</td>
                    <td class="address-cell">${member.address}</td>
                `;
                teamTableBody.appendChild(row);
            });
            
            // Show popup
            popupOverlay.classList.add('active');
        }

        // Function to close popup
        function closePopup() {
            popupOverlay.classList.remove('active');
        }

        // Add event listeners to all view team buttons
        viewTeamBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const productName = btn.getAttribute('data-product');
                openTeamPopup(productName);
            });
        });

        // Close popup when close button is clicked
        closeBtn.addEventListener('click', closePopup);

        // Close popup when clicking outside the content
        popupOverlay.addEventListener('click', (e) => {
            if (e.target === popupOverlay) {
                closePopup();
            }
        });

        // Close popup with Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && popupOverlay.classList.contains('active')) {
                closePopup();
            }
        });
    </script>
    <!-- Add this script just before </body> -->
<script>
  // Dropdown logic for login button
  document.addEventListener('DOMContentLoaded', function () {
    const dropdownToggle = document.querySelector('.dropdown-toggle');
    const dropdownMenu = document.querySelector('.dropdown-menu');
    let dropdownOpen = false;

    dropdownToggle.addEventListener('click', function (e) {
      e.stopPropagation();
      dropdownMenu.style.display = dropdownOpen ? 'none' : 'flex';
      dropdownOpen = !dropdownOpen;
      dropdownToggle.setAttribute('aria-expanded', dropdownOpen);
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function () {
      if (dropdownOpen) {
        dropdownMenu.style.display = 'none';
        dropdownOpen = false;
        dropdownToggle.setAttribute('aria-expanded', 'false');
      }
    });

    // Optional: Close dropdown with Escape key
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && dropdownOpen) {
        dropdownMenu.style.display = 'none';
        dropdownOpen = false;
        dropdownToggle.setAttribute('aria-expanded', 'false');
      }
    });
  });
</script>
<style>
  /* Dropdown styles for login button */
  #button-container .dropdown {
    position: relative;
    width: 100%;
  }
  #button-container .dropdown-menu a:hover {
    background: #f3f4f6;
    color: #2563eb;
  }
</style>
</body>
</html>