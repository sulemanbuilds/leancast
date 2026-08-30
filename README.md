<div align="center">

# 🎥 Lean Cast

**A lightweight two-smartphone live broadcasting system.**

A cloud-based broadcasting concept designed to help independent reporters, freelance journalists, and small news channels create professional live broadcasts using just two smartphones.

<br>

</div>

---

## ✨ What is Lean Cast?

Lean Cast is a lightweight, cloud-based live broadcasting system built around **two smartphones**.

One smartphone is used by the **reporter to capture the live broadcast**, while the second smartphone acts as a **broadcast control panel**.

The operator can control professional broadcast elements such as lower thirds, breaking-news banners, headlines, reporter information, locations, channel branding, and scrolling tickers without interrupting the live broadcast.

The goal is simple:

**📱 Reporter Phone → ☁️ Lean Cast → 📱 Control Phone**

Two smartphones provide the core tools needed to manage a lightweight live broadcast setup without traditional broadcast equipment.

---

## 🚀 Core Idea

```text
┌─────────────────────┐
│      📱 PHONE 1     │
│      Reporter       │
│                     │
│   Live Broadcast    │
└──────────┬──────────┘
           │
           │
           ▼
    ☁️ LEAN CAST
   Broadcasting System
           ▲
           │
           │
┌──────────┴──────────┐
│      📱 PHONE 2     │
│      Operator       │
│                     │
│   Broadcast Control │
└─────────────────────┘
```

### 📱 Phone 1 — Reporter

The first smartphone is used by the reporter to capture and transmit the live broadcast.

### 📱 Phone 2 — Operator

The second smartphone acts as the broadcast control interface.

The operator can manage:

* Headlines
* Breaking-news banners
* Lower thirds
* Reporter names
* Reporter roles
* Locations
* Scrolling tickers
* Channel branding
* Overlay visibility

The two devices work together through the Lean Cast system.

---

## 🎯 The Problem

Independent reporters, freelance journalists, and small news channels often need professional live coverage but may not have access to expensive broadcast equipment or dedicated production teams.

Traditional broadcasting can require:

* Professional cameras
* Dedicated graphics systems
* Production hardware
* Powerful computers
* Complicated software
* Multiple technical operators

This can make professional live broadcasting difficult for small teams, especially during field reporting and breaking-news situations.

---

## 💡 The Solution

Lean Cast reduces the complexity of live broadcasting by using **two smartphones as the foundation of the system**.

Instead of requiring a traditional broadcast production setup:

```text
Traditional Setup
Cameras
+ Production Hardware
+ Graphics System
+ Technical Setup
```

Lean Cast focuses on:

```text
Two Smartphones
+ Cloud-Based System
+ Broadcast Controls
```

The result is a more portable and accessible approach to live broadcasting.

---

## ⚡ Core Features

### 🎥 Two-Smartphone Broadcasting

Use one smartphone for reporting and another smartphone for broadcast control.

### 🎨 Professional Broadcast Overlays

Manage broadcast graphics such as:

* Lower thirds
* Breaking-news banners
* Headlines
* Reporter information
* Locations
* Channel branding
* Scrolling tickers

### 📡 Remote Broadcast Control

The operator can manage broadcast information from the second smartphone while the reporter continues the live coverage.

### ⚡ Dynamic Overlay Updates

Broadcast information can be changed during an active broadcast without requiring the broadcast screen to be manually refreshed.

### 📱 Mobile-First Control

The control interface is designed specifically for smartphone use so a small reporting team can operate the system from the field.

---

## 🏗️ Current Architecture

```text
                  LEAN CAST
              Broadcasting System

       ┌─────────────────────────┐
       │                         │
       │     Broadcast State     │
       │     & Overlay Data      │
       │                         │
       └───────────┬─────────────┘
                   │
          ┌────────┴────────┐
          │                 │
          ▼                 ▼

   📱 PHONE 1            📱 PHONE 2
   Broadcast Screen      Control Screen
         │                     │
         │                     │
         │                Operator controls
         │                     │
         └─────── Live ───────┘
```

The current MVP uses a simple request-based communication model.

The control interface sends changes to the backend, while the broadcast screen checks for the latest broadcast state and updates its overlays without a full page reload.

---

## 🛠️ Current Technology

Lean Cast's current MVP is intentionally lightweight.

### Frontend

* HTML
* CSS
* JavaScript
* AJAX
* Bootstrap where useful

### Backend

* Core PHP

### Data

* Lightweight server-side broadcast state

The project currently does **not require**:

* MySQL
* WebSockets
* AI models
* Complex backend frameworks

The architecture is intentionally kept simple so the core broadcasting workflow can be developed and tested quickly.

---

## 🎯 Initial Target Market

Lean Cast is initially focused on:

* Independent reporters
* Freelance journalists
* Small news channels
* Local media organizations
* Small reporting teams
* Independent broadcasters

These users often need professional live coverage while working with limited equipment, budget, and technical resources.

---

## 🏆 Hackathon Project

Lean Cast was developed for the **AI Hackathon by Al-Khidmat Foundation Pakistan in collaboration with Alibaba Cloud**.

The project was created as a practical exploration of how cloud-based technology can reduce the complexity and cost of live broadcasting.

Lean Cast was selected during the initial hackathon evaluation and is being further developed as part of the revised project submission.

---

## 🔮 Future Expansion

The current MVP focuses on the core two-smartphone broadcasting system.

Future versions can expand into additional markets and capabilities such as:

### New Markets

* Educational institutions
* Event organizers
* NGOs and community organizations
* Corporate broadcasting
* Emergency and public-information broadcasting
* Content creators
* Sports and community events

### Future Technology

* AI-assisted broadcasting
* AI camera switching
* Smart framing
* Automatic captions
* Multilingual translation
* Noise reduction
* AI-generated highlights
* Stream analytics
* Advanced broadcast automation

AI is considered a **future expansion**, not a required part of the current MVP.

---

<div align="center">

## 🎥 Two smartphones. One lean broadcasting system.

**Lean Cast — Making professional live broadcasting more accessible.**

<br>

Made with ❤️ by the Lean Cast team

</div>
